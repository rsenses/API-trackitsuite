<?php

/**
 * TODO: Configurar external Request para cada Company
 */

namespace App;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\FullCapacityException;
use App\Exceptions\NotFoundException;
use App\Exceptions\VerifiedException;

class Registration extends Model
{
    protected $table = 'registration';
    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'product_id',
        'customer_id'
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

    public function type()
    {
        return $this->belongsTo('App\RegistrationType', 'registration_type_id');
    }

    public function verifications()
    {
        return $this->hasMany('App\Verification', 'registration_id');
    }

    /**
     * Create a registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product $product
     * @param  App\Customer $customer
     * @return App\Registration
     */
    public static function createOrUpdate(Request $request, Product $product, Customer $customer)
    {
        $user = $request->user();

        $roomId = $product->users()->find($user->user_id) ? $product->users()->find($user->user_id)->pivot->room_id : null;

        // $registration->guardAgainstFullCapacity($request, $product, $roomId);

        $registration = Registration::firstOrNew(
            [
                'product_id' => $product->product_id,
                'customer_id' => $customer->customer_id,
            ]
        );

        $registration->registration_type_id = $request->registration_type_id;
        $registration->is_authorized = $request->authorized ?: 0;
        $registration->is_cancelled = 0;

        if (!$registration->exists) {
            $last = $product->registrations()->latest()->first()->metadata['orden'] ?? 0;

            $registration->unique_id = uniqid();
            $registration->metadata = [
                'orden' => $last + 1
            ];
        }

        $registration->save();

        $registration->saveRoomAccess($roomId);

        $registration->verifyRegistration($request);

        $registration->sendCreateRequest($request);

        return $registration;
    }

    /**
     * Create a registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public static function lookForRegistration(Request $request)
    {
        try {
            $registration = Registration::with(['customer', 'type'])
                ->where('is_authorized', 1)
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
     * Send the new registration to external source
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function sendCreateRequest(Request $request)
    {
        $params = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'registration_type' => $this->type->name,
            'unique_id' => $this->unique_id,
            'attended' => $this->verification,
        ];

        $noSendData = ['_token', 'email', 'first_name', 'last_name', 'registration_type_id', 'verification'];

        foreach ($request->except($noSendData) as $key => $value) {
            $params[$key] = $value;
        }

        $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'create', $params);
    }

    /**
     * Send verification to external source
     *
     * @return mixed
     */
    public function sendVerifyRequest()
    {
        $params = [
            'unique_id' => $this->unique_id,
        ];

        $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'attended', $params);
    }

    /**
     * Send POST request to external source
     *
     * @param  $baseUri
     * @param  $path
     * @param  array $params
     * @return mixed
     */
    public function sendPostRequest($baseUri, $path, array $params)
    {
        $client = new Client([
            'base_uri' => $baseUri
        ]);

        try {
            $response = $client->post($path, [
                'form_params' => $params
            ]);

            return true;
        } catch (RequestException $e) {
            Log::notice($e->getMessage());

            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
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
     * Vaerify the registration if necesary
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function verifyRegistration(Request $request)
    {
        if ($request->verification) {
            $verification = Verification::create([
                'registration_id' => $this->registration_id,
                'user_id' => $request->user()->user_id,
                'params' => ['method' => 'create']
            ]);

            return $verification;
        }

        return false;
    }

    /**
     * Verify registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function verify(Request $request)
    {
        $verification = $this->verifications()->latest()->first();

        if ($verification) {
            if ($verification->created_at > Carbon::now()->subSeconds(60)) {
                throw new VerifiedException(__('messages.verified'));
            }
        } else {
            $this->sendVerifyRequest();

            $verification = Verification::create([
                'registration_id' => $this->registration_id,
                'user_id' => $request->user()->user_id,
                'params' => $request->all()
            ]);
        }

        return $verification;
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
