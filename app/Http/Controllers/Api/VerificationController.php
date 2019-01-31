<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        $registration = Registration::getRegistrationByUniqueID($request);

        try {
            $registration->guardAgainstAlreadyVerifiedRegistration();
        } catch (\Throwable $th) {
            throw new HttpException(401, $th->getMessage(), $th, ['Error-Level' => 'warning']);
        }

        try {
            $registration->transition('verify');
        } catch (\Throwable $th) {
            Log::notice($th->getMessage());
        }

        return $registration;
    }
}
