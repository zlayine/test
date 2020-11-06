<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign_stats extends Model
{
    protected $table = 'campaign_stats';
    public $timestamps = false;

    public function campaign()
    {
        return $this->belongsTo('App\Campaign');
    }

}
