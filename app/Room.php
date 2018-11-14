<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';
    protected $primaryKey = 'room_id';

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function registrations()
    {
        return $this->belongsToMany('App\Registration', 'registration_room', 'room_id', 'registration_id')
            ->withPivot('permission');
    }
}
