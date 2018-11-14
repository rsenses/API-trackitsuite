<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'verification';
    protected $primaryKey = 'verification_id';

    protected $fillable = [
        'registration_id',
        'user_id',
        'remote_log',
        'params'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'remote_log' => 'array',
        'params' => 'array',
    ];

    public function registration()
    {
        return $this->belongsTo('App\Registration');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
