<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $hidden = ['pivot'];
    
    public function type()
    {
        return $this->belongsTo('App\Type');
    }

    public function email_lists()
    {
        return $this::where('type_id', 1)->get();
    }

    public function campaigns()
    {
        return $this->belongsToMany('App\Campaign','campaign_files','campaign_id','file_id');
    }
}
