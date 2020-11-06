<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $hidden = ['pivot'];
    
    public function files()
    {
        return $this->belongsToMany('App\File','campaign_files','campaign_id','file_id');
    }

    public function stats()
    {
        return $this->hasOne('App\Campaign_stats');
    }

}
