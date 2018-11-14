<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';

    public function users()
    {
        return $this->belongsToMany('App\User', 'company_user', 'company_id', 'user_id')
            ->withPivot('role');
    }
}
