<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Registration;
use App\Product;
use App\Customer;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $registrations = $product->registrations()
            ->select('registration.*')
            ->with('customer', 'customer.metas')
            ->join('customer', 'registration.customer_id', '=', 'customer.customer_id')
            ->where('registration.is_confirmed', 1)
            ->registrationType($request->registration_type_id)
            ->orderBy('customer.last_name', 'ASC')
            ->get();

        if (!$registrations) {
            abort(404, __('messages.no_product'));
        }

        return $registrations;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            'nif' => 'nullable|max:255',
            'product_id' => 'required|exists:product,product_id',
            'registration_type_id' => 'required|exists:registration_type,registration_type_id',
            'authorized' => 'nullable|boolean',
            'verification' => 'required|boolean',
        ]);

        $product = Product::findOrFail($request->product_id);

        $customer = Customer::createOrUpdate($request);

        $registration = Registration::createOrUpdate($request, $product, $customer);

        try {
            $registration->transition('create');
        } catch (\Throwable $th) {
            Log::notice($th->getMessage());
        }

        return $registration;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Registration::with('customer')->findOrFail($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);

        $registration->is_cancelled = 1;

        $registration->save();

        return $registration;
    }
}
