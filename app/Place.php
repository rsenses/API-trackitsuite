<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'place';
    protected $primaryKey = 'place_id';

    public function state()
    {
        return $this->hasOne('App\State');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
