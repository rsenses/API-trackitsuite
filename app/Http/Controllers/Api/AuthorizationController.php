<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use Illuminate\Support\Facades\Log;

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
            'transition' => 'required|in:approve,reject,cancel',
        ]);

        $registration = Registration::findOrFail($id);

        try {
            $registration->transition($request->transition);
        } catch (\Throwable $th) {
            Log::notice($th->getMessage());
        }

        return $registration;
    }
}
