<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Login api user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);


        if (User::where('email', $request->get('email'))->exists()) {
            $user = User::where('email', $request->get('email'))->first();

            $auth = Hash::check($request->get('password'), $user->password);

            if ($user && $auth) {
                $this->authenticated($request, $user);

                return response([
                    'api_token' => $user->api_token,
                    'message' => __('messages.authorized'),
                ]);
            }
        }

        return response([
            'message' => __('messages.unauthorized'),
        ], 401);
    }

    protected function authenticated(Request $request, User $user)
    {
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
    }
}
