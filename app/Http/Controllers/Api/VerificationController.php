<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;

class VerificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'unique_id' => 'required|exists:registration,unique_id',
        ]);

        $registration = Registration::lookForRegistration($request);

        // $registration->guardAgainstNotAuthorizedAccess($request);

        $registration->verify($request);

        return $registration;
    }
}
