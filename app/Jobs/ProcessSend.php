<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\SendJob;
use App\Smtps_group;
use App\Campaign;

use Illuminate\Support\Facades\Redis;

class ProcessSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 2;
    protected $campaign;
    protected $debug;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign, $debug = 0)
    {
        $this->campaign = $campaign;
        $this->debug = $debug;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $campaign = Campaign::with('files')->where('id', $this->campaign)->first();
        $emails_files = $campaign->files()->where('type_id', 1)->get();
        $stats = $campaign->stats;
        $campaign = $this->random_detect($campaign);
        $campaign = $this->random_detect($campaign, 1);
        $campaign = $this->encodes($campaign);
        $campaign = json_decode($campaign);
        $attachements = $this->getAttachements($campaign->files);
        $smtps = Smtps_group::find($campaign->smtp_group)->smtps;
        $rot = $campaign->rotation;
        $smtps_count = sizeof($smtps);
        $step_rot = 0;
        $step_smtp = 0;
        if ($this->debug == 1)
        {
            $recp = array(env('EMAIL_DEBUG'), 'Debug User');
            //$recp = array('zolay1996@gmail.com', 'Debug User');
            SendJob::dispatchNow($campaign, $stats, $recp, $attachements, $smtps[0], 1);
        }
        else
        {
            foreach($emails_files as $file)
            {
                $handle = fopen(storage_path('app/public/' . $file->path), "r");
                if ($handle) {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        if (strlen($buffer) > 5){
                            if(strpos($buffer, ';') !== false){
                                $stats->pending++;
                                $stats->save();
                                $recp = explode(";", $buffer);
                                //$recp = $this->fixName($recp);
                                SendJob::dispatch($campaign, $stats->id, $recp, $attachements, $smtps[$step_smtp], 0);
                                $step_rot++;
                                if($step_rot == $rot)
                                {
                                    $step_smtp++;
                                    $step_rot = 0;
                                }
                                if($step_smtp == $smtps_count)
                                    $step_smtp = 0;
                            }
                            else
                            {
                                $stats->failed++;
                                $stats->save();
                            }
                        }
                        else
                        {
                            $stats->failed++;
                            $stats->save();
                        }
                    }
                }
            }
        }
    }

    function fixName($recp)
    {
        if ($recp[1])
        {
            if (!strpos($recp[1], "\n"))
            {
                $recp[1] = $recp[1] . "\n";
            }
        }
        return $recp;
    }

    function getAttachements($data){
        $array = array();
        foreach($data as $file){
            if ($file->type_id == 2)
                array_push($array, $file);
        }
        return $array;
    }

    function generateRandomA($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateRandomAN($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateRandomN($length = 10) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';
        return preg_replace($from, $to, $content, 1);
    }

    function random_detect($content="", $fix = 0)
    {   
        if ($fix)
            $re = '/\[f([aA-zZ]{1,2})(\d+)]/m';
        else
            $re = '/\[([aA-zZ]{1,2})(\d+)]/m';
        $str = $content;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match)
        {
            switch($match[1])
            {
                case "N":
                case "n":
                    if ($fix)
                        $str = str_replace($match[0], $this->generateRandomN((int)$match[2]) , $str);
                    else
                        $str = $this->str_replace_first($match[0], $this->generateRandomN((int)$match[2]) , $str);
                break;
                case "A":
                    if ($fix)
                        $str = str_replace($match[0], $this->generateRandomA((int)$match[2]) , $str);
                    else
                        $str = $this->str_replace_first($match[0], $this->generateRandomA((int)$match[2]) , $str);
                break;
                case "a":
                    if ($fix)
                        $str = str_replace($match[0], $this->generateRandomA((int)$match[2]) , $str);
                    else
                        $str = $this->str_replace_first($match[0], strtolower( $this->generateRandomA((int)$match[2])) , $str);
                break;
                case "NA":
                case "AN":
                    if ($fix)
                        $str = str_replace($match[0], $this->generateRandomAN((int)$match[2]) , $str);
                    else
                        $str = $this->str_replace_first($match[0], $this->generateRandomAN((int)$match[2]) , $str);
                break;
                case "an":
                case "na":
                    if ($fix)
                        $str = str_replace($match[0], $this->generateRandomAN((int)$match[2]) , $str);
                    else
                        $str = $this->str_replace_first($match[0], strtolower($this->generateRandomAN((int)$match[2])) , $str);
                break;
            }
        }
        return $str;
    }

    function encodes($content)
    {
        $re = '/\[([ub]{1})\'(.+)\']/m';
        $str = $content;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match)
        {
            switch($match[1])
            {
                case "u":
                    $str = str_replace($match[0], url_encode($match[2]) , $str);
                break;
                case "b":
                    $str = str_replace($match[0], base64_encode($match[2]) , $str);
                break;
            }
        }
        return $str;
    }

}
