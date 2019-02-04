<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\FullCapacityException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicatedException;
use App\Exceptions\AlreadyVerifiedException;
use App\Traits\Statable;

class Registration extends Model
{
    use Statable;

    const HISTORY_MODEL = [
        'name' => 'App\RegistrationState', // the related model to store the history
        'foreign_key' => 'registration_id' // the foreign key to relation
    ];
    const SM_CONFIG = 'registration'; // the SM graph to use

    protected $table = 'registration';
    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'product_id',
        'customer_id'
    ];

    protected $hidden = [
        'updated_at',
        'customer_id',
        'product_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Room', 'registration_room', 'registration_id', 'room_id')
            ->withPivot('permission');
    }

    /**
     * Scope a query to filter for registration_type if exist
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
    }

    /**
     * Create a registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product $product
     * @param  App\Customer $customer
     * @return App\Registration
     */
    public static function make(Request $request, Product $product, Customer $customer)
    {
        $user = $request->user();

        $roomId = $product->users()->find($user->user_id) ? $product->users()->find($user->user_id)->pivot->room_id : null;

        $registration = Registration::firstOrNew(
            [
                'product_id' => $product->product_id,
                'customer_id' => $customer->customer_id,
            ]
        );

        $registration->guardAgainstAlreadyRegister();

        $registration->unique_id = !empty($request->unique_id) ? $request->unique_id : uniqid();

        $last = $product->registrations()->latest()->first()->metadata['orden'] ?? 0;
        $registration->metadata = [
            'orden' => $last + 1
        ];

        $registration->type = strtolower($request->registration_type);

        $registration->save();

        $registration->saveRoomAccess($roomId);

        return $registration->fresh();
    }

    /**
     * Get Registration by Unique ID
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public static function getRegistrationByUniqueID(Request $request)
    {
        try {
            $registration = Registration::with(['customer'])
                ->where(function ($q) {
                    $q->where('state', 'accepted');
                    $q->orWhere('state', 'verified');
                })
                ->where('unique_id', $request->unique_id)
                ->whereHas('product.users', function ($query) use ($request) {
                    $query->where('user.user_id', $request->user()->user_id);
                })
                ->firstOrFail();

            return $registration;
        } catch (\Throwable $th) {
            throw new NotFoundException(__('messages.not_found'));
        }
    }

    /**
     * Save the room access if exists
     *
     * @param  $roomId
     * @return mixed
     */
    public function saveRoomAccess($roomId)
    {
        if ($roomId) {
            $room = Room::find($roomId);

            $this->rooms()->save($room, [
                'permission' => 1
            ]);

            return $room;
        }

        return false;
    }

    /**
     * Check if the registration has benn verified recently, we gave it a 60 seconds margin in case of accidental double validation
     *
     * @return mixed
     */
    public function guardAgainstAlreadyVerifiedRegistration()
    {
        if ($this->state == 'verified') {
            throw new AlreadyVerifiedException(__('messages.verified'));
        }
    }

    /**
     * Check if the registration exists
     *
     * @param  App\Verification  $verification
     * @return mixed
     */
    public function guardAgainstAlreadyRegister()
    {
        if ($this->exists) {
            throw new DuplicatedException(__('messages.duplicated'));
        }
    }

    /**
     * Check if there is enough capacity on the room
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function guardAgainstNotAuthorizedAccess(Request $request)
    {
        $user = $request->user();

        $roomId = $product->users()->find($user->user_id) ? $product->users()->find($user->user_id)->pivot->room_id : null;

        if ($roomId && $registration->rooms()->count() && $registration->rooms()->find($roomId)->pivot->permission === 0) {
            throw new UnauthorizedException(__('messages.unauthorized'));
        }
    }

    /**
     * Check if there is enough capacity on the room
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product $product
     * @return mixed
     */
    public function guardAgainstFullCapacity(Request $request, Product $product, $roomId)
    {
        if ($roomId) {
            $registrationRoom = Registration::whereHas('product', function ($query) use ($productId) {
                $query->where('product.product_id', $productId);
            })
                ->whereHas('rooms', function ($query) use ($roomId) {
                    $query->where('room.room_id', $roomId);
                })
                ->get();

            if ($registrationRoom->count() >= $product->rooms->find($roomId)->capacity) {
                throw new FullCapacityException(__('messages.sold_out'));
            }
        }
    }
}
