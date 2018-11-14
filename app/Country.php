<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo('App\State');
    }
}
