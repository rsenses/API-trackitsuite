<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'state';
    protected $primaryKey = 'state_id';

    public function country()
    {
        return $this->hasOne('App\Country');
    }

    public function place()
    {
        return $this->belongsTo('App\Place');
    }
}
