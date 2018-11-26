<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationState extends Model
{
    protected $table = 'registration_states';

    protected $fillable = ['transition', 'from', 'user_id', 'registration_id', 'to'];

    public function registration()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
