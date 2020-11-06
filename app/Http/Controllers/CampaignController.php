<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP as PHPSmtp;
use PHPMailer\PHPMailer\Exception;
use Date;
use Carbon\Carbon;
use App\Campaign;
use App\Smtp;
use App\Smtps_group;
use App\File;
use App\Type;
use App\Campaign_stats;
use App\Events\StatusEvent;
use App\Events\StatsEvent;
use App\Jobs\ProcessSend;

class CampaignController extends Controller
{
    function getCampaigns()
    {
        $campaigns = Campaign::select('id','name','subject','smtp_group','status','created_at')->with('files')->get();
        return response()->json(['campaigns' => $campaigns]);
    }

    function getCampaign($id)
    {
        $campaign = Campaign::with('files')->where('id', $id)->first();
        $campaign->smtp_group = Smtps_group::find($campaign->smtp_group);
        return response()->json(['campaign' => $campaign]);
    }

    function saveCampaign(Request $request)
    {
        $data = (object) $request->campaign;
        if ($data->id != 0){
            $campaign = Campaign::find($data->id);
            if(!$campaign)
                $campaign = new Campaign();
        }
        else
            $campaign = new Campaign();
        $campaign->name = $data->name;
        $campaign->smtp_group = $data->smtp['id'];
        $campaign->rotation = $data->rotation;
        $campaign->charset = $data->charset;
        $campaign->encoding = $data->encoding;
        $campaign->encryption = $data->encryption;
        $campaign->content_type = $data->content;
        $campaign->helo = $data->helo;
        $campaign->from_email = $data->from_email;
        $campaign->from_name = $data->from_name;
        $campaign->subject = $data->subject;
        $campaign->header = $data->header;
        $campaign->body = $data->body;
        $campaign->alt = $data->alt;
        $campaign->cal = $data->cal;
        $campaign->iscal = $data->iscal;
        $campaign->status = 0;
        $campaign->auto_tls = $data->auto_tls;
        $campaign->save();
        $campaign->files()->detach();
        if (sizeof($data->files))
        {
            foreach($data->files as $file)
            {
                $campaign->files()->attach($file['id']);
            }
        }
        if ($data->id == 0)
            $campaign = Campaign::with('files')->latest()->first();
        else
        {
            $campaign = Campaign::with('files');
            $campaign = $campaign->where('id', $data->id)->first();
        }
        return response()->json(['campaign' => $campaign, 'message' => 'Campaign saved!']);
    }

    public static function rfcDate()
    {
        date_default_timezone_set(@date_default_timezone_get());
        return date('D, j M Y H:i:s O');
    }

    public function getEncryptionPort($enc){
        if ($enc == 'tls')
            return 587;
        else if ($enc == 'ssl');
            return 443;
        return 25;
    }

    function getAttachements($data){
        $array = array();
        foreach($data as $file){
            if ($file->type_id == 2)
                array_push($array, $file);
        }
        return $array;
    }

    function launchCampaign($id)
    {
        $campaign = Campaign::with('files')->where('id', $id)->first();
        $campaign->status = 1;
        $campaign->save();
        event(new StatusEvent($campaign));
        if (!$campaign->stats)
            $stats = new Campaign_stats();
        else
            $stats = $campaign->stats;
        $stats->campaign_id = $campaign->id;
        $stats->pending = 0;
        $stats->completed = 0;
        $stats->failed = 0;
        $stats->save();
        ProcessSend::dispatchNow($campaign->id, 0);
        return response()->json(['message' => 'Campaign launched!', 'error' => 0]);
    }

    function launchDebug($id)
    {
        $campaign = Campaign::with('files')->where('id', $id)->first();
        $campaign->status = 1;
        $campaign->save();
        event(new StatusEvent($campaign));
        if (!$campaign->stats)
            $stats = new Campaign_stats();
        else
            $stats = $campaign->stats;
        $stats->campaign_id = $campaign->id;
        $stats->pending = 0;
        $stats->completed = 0;
        $stats->failed = 0;
        $stats->save();
        ProcessSend::dispatchNow($campaign->id, 1);
        return response()->json(['message' => 'Campaign launched!', 'error' => 0]);
    }

    function retryCampaign($id)
    {
        $c = Campaign::find($id);
        $c->status = 0;
        $c->save();
        event(new StatusEvent($c));
        $stats = $c->stats;
        $stats->pending = 0;
        $stats->completed = 0;
        $stats->failed = 0;
        $stats->save();
        event(new StatsEvent($stats));
    }

    function getStats()
    {
        $stats = Campaign_stats::all();
        return response()->json(['stats' => $stats]);
    }

    function getStatsById($id)
    {
        $stats = Campaign::find($id)->stats;
        return response()->json(['stats' => $stats]);
    }

    // function launchCampaign($id)
    // {
    //     $iscal = false;
    //     $campaign = Campaign::with('files')->where('id', $id)->first();
    //     if($this->check_campaign($campaign))
    //         return response()->json(['message' => 'Campaign is not complete', 'error' => 1]);
    //     $campaign->status = 1;
    //     $campaign->save();
    //     $emails_files = $campaign->files()->where('type_id', 1)->get();
        
    //     $campaign = $this->random_detect($campaign);
    //     $campaign = $this->random_detect($campaign, 1);
    //     $campaign = $this->encodes($campaign);
    //     $campaign = json_decode($campaign);
        
    //     $attachements = $this->getAttachements($campaign->files);
    //     if ($campaign->iscal)
    //         $iscal = true;
    //     $smtps = Smtps_group::find($campaign->smtp_group)->smtps;
    //     $rot = $campaign->rotation;
    //     $smtps_count = sizeof($smtps);
    //     $step_rot = 0;
    //     $step_smtp = 0;
    //     foreach($emails_files as $file)
    //     {
    //         $handle = fopen(storage_path('app/public/' . $file->path), "r");
    //         if ($handle) {
    //             while (($buffer = fgets($handle, 4096)) !== false) {
    //                 if (strlen($buffer) > 5){
    //                     if(strpos($buffer, ';') !== false){
    //                         $recp = explode(";", $buffer);
    //                         if($recp[0] != ""){
    //                             $recp[0] = strtolower(rtrim($recp[0]));

    //                             $mail = new PHPMailer(true);
    //                             $mail->SMTPDebug = 1;
    //                             $mail->isSMTP();
    //                             $mail->Host       = 'smtp.ionos.com';
    //                             $mail->SMTPAuth   = true;
    //                             //get data from $smtps[$step_smtp]
    //                             $mail->Username = 'info@awih.ca';
    //                             $mail->Password = '12345678';

    //                             $mail->SMTPSecure = $campaign->encryption;
    //                             $mail->Port       = $this->getEncryptionPort($campaign->encryption);

    //                             $mail->From =  $campaign->from_email;
    //                             $mail->FromName = $campaign->from_name;

    //                             $mail->CharSet = $campaign->charset;
    //                             $mail->Encoding = $campaign->encoding;
    //                             $recp_name = '';
    //                             if ($recp[1] && $recp[1] != "\n")
    //                                 $recp_name = $recp[1];
    //                             //$mail->MessageID = "<" . md5('HELLO' . (idate("U") - 1000000000) . uniqid()) . '-' . '@facebooko.com>';                              // TCP port to connect to
    //                             $mail->addAddress($recp[0], $recp_name);
                            
    //                             $mail->isHTML(true);
    //                             $mail->Subject = $campaign->subject;
    //                             if ($iscal || strpos($campaign->content_type, 'html'))
    //                                 $mail->Body    = $campaign->body;
    //                             else if ($iscal || strpos($campaign->content_type, 'plain'))
    //                                 $mail->AltBody = $campaign->body;
    //                             if ($iscal)
    //                                 $mail->Ical = $campaign->cal;
    //                             foreach($attachements as $attachement)
    //                             {
    //                                 if (!strpos($campaign->body, $attachement->name))
    //                                     $mail->addAttachment(storage_path('app/public/' . $attachement->path), $attachement->name);
    //                                 else
    //                                     $mail->AddEmbeddedImage(storage_path('app/public/' . $attachement->path), $attachement->name);
    //                             }
    //                             $header = $campaign->header;
    //                             $received = self::rfcDate();
    //                             $a = array(
    //                                 '[from_name]',
    //                                 '[from_email]',
    //                                 '[subject]',
    //                                 '[to]',
    //                                 '[mail_date]'
    //                             );
                                
    //                             $b = array(
    //                                 $campaign->from_name,
    //                                 $campaign->from_email,
    //                                 $campaign->subject,
    //                                 "$recp_name <$recp[0]>",
    //                                 $received
    //                             );
    //                             $header = str_replace($a, $b, $header);
    //                             $header = array_filter(array_map('trim', explode("\n", $header)));
    //                             foreach ($header as $key => $value) {
    //                                 $keys = preg_split("/[:]+/", trim($value), '2');
    //                                 $mail->addCustomHeader($keys[0], $keys[1]);
    //                             }
    //                             $step_rot++;
    //                             if ($mail->send()) {
    //                                 echo 'Message has been sent<br>';
    //                                 //return response()->json(['message' => 'Message has been sent', 'error' => 0]);
    //                             } else {
    //                                 echo "Something Wrong";
    //                                 return response()->json(['message' => 'Something wrong', 'error' => 1]);
    //                             }
    //                             if($step_rot == $rot)
    //                             {
    //                                 $step_smtp++;
    //                                 $step_rot = 0;
    //                             }
    //                             if($step_smtp == $smtps_count)
    //                                 $step_smtp = 0;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         fclose($handle);
    //     }
    //     return "OK";
    // }
    
    function deleteCampaign($id)
    {
        $campaign = Campaign::find($id);
        $campaign->files()->detach();
        $campaign->delete();
        return response()->json(['message' => 'Campaign deleted!']);
    }

    function duplicateCampaign($id)
    {
        $oc = Campaign::find($id);
        $campaign = new Campaign();
        $campaign->name = $oc->name . " Copy";
        $campaign->smtp_group = $oc->smtp_group;
        $campaign->rotation = $oc->rotation;
        $campaign->charset = $oc->charset;
        $campaign->encoding = $oc->encoding;
        $campaign->encryption = $oc->encryption;
        $campaign->content_type = $oc->content_type;
        $campaign->helo = $oc->helo;
        $campaign->from_email = $oc->from_email;
        $campaign->from_name = $oc->from_name;
        $campaign->subject = $oc->subject;
        $campaign->header = $oc->header;
        $campaign->body = $oc->body;
        $campaign->alt = $oc->alt;
        $campaign->cal = $oc->cal;
        $campaign->iscal = $oc->iscal;
        $campaign->status = 0;
        $campaign->auto_tls = $oc->auto_tls;
        $campaign->save();
        if (sizeof($oc->files))
        {
            foreach($oc->files as $file)
            {
                $campaign->files()->attach($file->id);
            }
        }
        $campaign = Campaign::with('files')->latest()->first();
        return response()->json(['campaign' => $campaign, 'message' => 'Campaign duplicated!']);
    }

}
