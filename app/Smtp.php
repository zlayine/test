<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Smtp extends Model
{
    public function group()
    {
        return $this->belongsTo('App\Smtps_group');
    }
}
