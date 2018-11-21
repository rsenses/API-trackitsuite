<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';

    protected $dates = [
        'date_start',
        'date_end',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'updated_at',
        'company_id',
        'place_id'
    ];

    public function place()
    {
        return $this->hasOne('App\Place', 'place_id');
    }

    /**
     * Get the template that owns the product.
     */
    public function template()
    {
        return $this->belongsTo('App\Template', 'template_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'product_user', 'product_id', 'user_id')
            ->withPivot('product_user_id', 'room_id', 'date_start', 'date_end');
    }

    public function registrations()
    {
        return $this->hasMany('App\Registration', 'product_id');
    }

    public function customers()
    {
        return $this->belongsToMany('App\Customer', 'registration')
            ->withPivot('unique_id', 'product_user_id', 'verified_time', 'created_at');
    }

    public function rooms()
    {
        return $this->hasMany('App\Room', 'room_id');
    }

    public function logs()
    {
        return $this->hasMany('App\Log');
    }
}
