<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationType extends Model
{
    protected $table = 'registration_type';
    protected $primaryKey = 'registration_type_id';

    public function registration()
    {
        return $this->hasMany('App\Registration');
    }
}
