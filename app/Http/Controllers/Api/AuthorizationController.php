<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use App\Events\RegistrationAuthorized;

class AuthorizationController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'is_authorized' => 'required|boolean',
        ]);

        $registration = Registration::findOrFail($id);

        $registration->is_authorized = $request->is_authorized;

        $registration->save();

        $registration->transition('approve');

        event(new RegistrationAuthorized($registration));

        return $registration;
    }
}
