<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Smtps_group extends Model
{
    protected $table = 'smtps_group';

    public function smtps()
    {
        return $this->hasMany('App\Smtp', 'group_id');
    }

}
