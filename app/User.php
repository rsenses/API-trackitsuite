<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'first_name', 'last_name', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'email_verified_at',
        'updated_at',
        'is_admin',
        'is_moderator',
        'uuid'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_user', 'user_id', 'product_id')
            ->withPivot('product_user_id', 'room_id', 'date_start', 'date_end');
    }

    public function companies()
    {
        return $this->belongsToMany('App\Company', 'company_user', 'user_id', 'company_id')
            ->withPivot('role');
    }

    public function logs()
    {
        return $this->hasMany('App\Log');
    }

    public function verifications()
    {
        return $this->hasMany('App\Verification');
    }
}
