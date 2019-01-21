<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use Illuminate\Support\Facades\Log;

class AuthorizationCustomerController extends Controller
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
            'product' => 'required|integer|exists:product,product_id',
            'transition' => 'required|in:approve,reject,cancel,create',
        ]);

        $registration = Registration::where('product_id', $request->product)
            ->where('customer_id', $id)
            ->firstOrFail();

        try {
            $registration->transition($request->transition);
        } catch (\Throwable $th) {
            Log::notice($th->getMessage());
        }

        return $registration;
    }
}
