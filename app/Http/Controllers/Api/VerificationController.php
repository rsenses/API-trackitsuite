<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'unique_id' => 'required|exists:registration,unique_id',
        ]);

        $registration = Registration::getRegistrationByUniqueID($request);

        try {
            $registration->guardAgainstAlreadyVerifiedRegistration($request);
        } catch (\Throwable $th) {
            abort(403, $th->getMessage());
        }

        try {
            $registration->guardAgainstNotAuthorizedAccess($request);
        } catch (\Throwable $th) {
            abort(403, $th->getMessage());
        }

        try {
            $registration->transition('verify');
        } catch (\Throwable $th) {
            Log::notice($th->getMessage());
        }

        return $registration;
    }
}
