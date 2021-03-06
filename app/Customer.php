<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Product', 'registration')
            ->withPivot('unique_id', 'product_user_id', 'verified_time', 'created_at');
    }

    public function registrations()
    {
        return $this->hasMany('App\Registration');
    }

    public function metas()
    {
        return $this->hasMany('App\CustomerMeta', 'customer_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Update or create a customer and related meta data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return App\Customer
     */
    public static function createOrUpdate(Request $request)
    {
        $customer = Customer::updateOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]
        );

        $noCustomerMetaData = ['_token', 'email', 'first_name', 'last_name', 'product_id', 'registration_type', 'transition', 'unique_id', 'metadata'];

        foreach ($request->except($noCustomerMetaData) as $key => $value) {
            if ($value) {
                $customer->metas()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_value' => $value]
                );
            }
        }

        return $customer;
    }
}
