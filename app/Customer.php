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

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
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

        $noCustomerMetaData = ['_token', 'email', 'first_name', 'last_name', 'product_id', 'registration_type_id', 'authorized', 'verification', 'infothird', 'infomail', 'age', 'legal'];

        foreach ($request->except($noCustomerMetaData) as $key => $value) {
            $saveMetaData = $customer->metas()->create([
                'meta_key' => $key,
                'meta_value' => $value
            ]);
        }

        return $customer;
    }
}
