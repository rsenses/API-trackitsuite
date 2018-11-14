<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Place extends Model
{
    protected $table = 'place';
    protected $primaryKey = 'place_id';

    protected $fillable = [
        'name',
        'address',
        'city',
        'zip',
        'state_id',
        'slug',
        'company_id',
    ];

    protected $hidden = [
        'updated_at',
        'is_shareable',
        'company_id'
    ];

    public function state()
    {
        return $this->hasOne('App\State', 'state_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'place_id');
    }

    /**
     * Update or create a customer and related meta data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return App\Customer
     */
    public static function createOrUpdate(Request $request)
    {
        if ($request->place_name) {
            $place = Place::updateOrCreate(
                [
                    'name' => $request->place_name,
                    'address' => $request->place_address,
                    'city' => $request->place_city,
                    'company_id' => $request->company_id,
                ],
                [
                    'is_shareable' => 0,
                    'zip' => $request->place_zip,
                    'state_id' => $request->state_id,
                    'slug' => str_slug($request->place_name),
                ]
            );
        } else {
            $place = null;
        }

        return $place;
    }
}
