<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP as PHPSmtp;
use PHPMailer\PHPMailer\Exception;
use App\Events\StatsEvent;
use App\Events\StatusEvent;
use App\Events\DebugEvent;
use App\Campaign;
use App\Campaign_stats;

use Illuminate\Support\Facades\Redis;

class SendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $campaign;
    protected $recp;
    protected $attachements;
    protected $smtp;
    protected $stats;
    protected $debug_mode;
    public $tries = 5;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign, $stats, $recp, $attachements, $smtp, $debug)
    {
        $this->campaign = $campaign;
        $this->recp = $recp;
        $this->smtp = $smtp;
        $this->stats = $stats;
        $this->attachements = $attachements;
        $this->debug_mode = $debug;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $iscal = false;
        try {
            if ($this->debug_mode)
                ob_start();
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = ($this->debug_mode) ? 1 : 0;
            if ($this->debug_mode)
                $mail->Debugoutput = 'echo';
            $mail->isSMTP();
            $mail->Host       = $this->smtp->host;
            $mail->SMTPAuth   = true;
            $mail->Helo = $this->campaign->helo;
            $mail->SMTPAutoTLS = $this->campaign->auto_tls;
            echo $this->smtp->username . "\n";
            $mail->Username = $this->smtp->username;
            $mail->Password = $this->smtp->password;

            $mail->CharSet = $this->campaign->charset;
            $mail->Encoding = $this->campaign->encoding;
            $mail->SMTPSecure = $this->campaign->encryption;
            //$mail->Port       = $this->getEncryptionPort($this->campaign->encryption);
            $mail->Port       = $this->smtp->port;

            $mail->From =  $this->campaign->from_email;
            $mail->FromName = $this->campaign->from_name;
            
            $recp_name = '';
            if ($this->recp[1] && $this->recp[1] != "\n")
                $recp_name = $this->recp[1];
            $recp_name = str_replace("\n", "", $recp_name);
            if (!$recp_name)
                $recp_name = $this->getNameFromEmail($this->recp[0]);
            $mail->addAddress($this->recp[0], $recp_name);
            if ($this->campaign->iscal)
                $iscal = true;
            $mail->isHTML(true);
            $mail->Subject = $this->campaign->subject;
            $mail->Body    = $this->campaign->body;
            //if (strpos($this->campaign->content_type, 'multi') || (strpos($this->campaign->content_type, 'html') && sizeof($this->attachements)))
            if ($this->campaign->alt)
                $mail->AltBody = $this->campaign->alt;
            if ($iscal)
                $mail->Ical = $this->campaign->cal;
            foreach($this->attachements as $attachement)
            {
                if (!strpos($this->campaign->body, $attachement->name))
                    $mail->addAttachment(storage_path('app/public/' . $attachement->path), $attachement->name);
                else
                    $mail->AddEmbeddedImage(storage_path('app/public/' . $attachement->path), $attachement->name);
            }
            $header = $this->campaign->header;
            $received = self::rfcDate();
            $a = array(
                '[from_name]',
                '[from_email]',
                '[subject]',
                '[to]',
                '[mail_date]'
            );
            $recp_email = $this->recp[0];
            $b = array(
                $this->campaign->from_name,
                $this->campaign->from_email,
                $this->campaign->subject,
                "$recp_name <$recp_email>",
                $received
            );
            $header = str_replace($a, $b, $header);
            $header = $mail->do_encode_text($header);
            $header = array_filter(array_map('trim', explode("\n", $header)));
            foreach ($header as $key => $value) {
                $keys = preg_split("/[:]+/", trim($value), '2');
                $mail->addCustomHeader($keys[0], $keys[1]);
            }
            if (!$this->debug_mode)
                $stats = Campaign_stats::find($this->stats);
            if ($mail->send()) {
                if (!$this->debug_mode)
                {
                    $stats = Campaign_stats::find($this->stats);
                    if ($stats->pending != 0)
                    {
                        $stats->completed++;
                        $stats->pending--;
                        $stats->save();
                        event(new StatsEvent($stats));
                    }
                    if ($stats->pending == 0)
                    {
                        $c = Campaign::find($this->campaign->id);
                        $c->status = 2;
                        $c->save();
                        event(new StatusEvent($c));
                    }
                }
            } else {
                if (!$this->debug_mode)
                {
                    
                    $stats = Campaign_stats::find($this->stats);
                    if ($stats->pending != 0)
                    {
                    $stats->failed++;
                    $stats->pending--;
                    $stats->save();
                    event(new StatsEvent($stats));
                    } 
                    if ($stats->pending == 0)
                    {
                        $c = Campaign::find($this->campaign->id);
                        $c->status = 2;
                        $c->save();
                        event(new StatusEvent($c));
                    }
                }
            }
           
            if ($this->debug_mode)
            {
                $c = Campaign::find($this->campaign->id);
                $c->status = 0;
                $c->save();
                event(new StatusEvent($c));
                $list = ob_get_contents();
                ob_end_clean();
                $data = substr($list,0, 2100); 
                event(new DebugEvent($data));
                $data = substr($list,2100, 4200); 
                if ($data)
                    event(new DebugEvent($data));
            }
        } catch (Exception $e) {
            $c = Campaign::find($this->campaign->id);
            $c->status = 0;
            $c->save();
            event(new StatusEvent($c));
            if ($this->debug_mode)
            {
                $list = ob_get_contents();
                ob_end_clean();
                event(new DebugEvent($list));
            }
        }
    }

    function getNameFromEmail($email)
    {
        $email = trim($email);
        $name = explode( '@', $email)[0];
        if (strpos($name, '.'))
        {
            $tmp = $name;
            $name = str_replace('.', ' ', $name);
            $name = str_replace('-', ' ', $name);
            $name = preg_replace('/\d+/', '', $name);
            if (strlen($name) < 3)
                $name = $tmp;
            else
                $name[0] = strtoupper($name[0]);
        }
        return ($name);
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
}
