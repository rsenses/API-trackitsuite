<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerMeta extends Model
{
    protected $table = 'customer_meta';
    protected $primaryKey = 'customer_meta_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meta_key', 'meta_value'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
}
