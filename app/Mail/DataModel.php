<?php

namespace App\Mail;

use App\Registration;

trait DataModel
{
    /**
     * Get the property value for the given property.
     *
     * @param  \Registration  $registration
     * @return array
     */
    protected function getRegistrationData(Registration $registration)
    {
        return [
            'registration_unique_id' => $registration->unique_id,
            'registration_qr' => "https://trackitsuite.com/es/registration/qr/{$registration->unique_id}",
            'registration_metadata' => $registration->metadata,
            'registration_type' => $registration->type->name,
            'customer_first_name' => $registration->customer->first_name,
            'customer_last_name' => $registration->customer->last_name,
            'customer_email' => $registration->customer->email,
            'product_name' => $registration->product->name,
            'product_image' => $registration->product->image,
            'product_description' => $registration->product->description,
            'product_date_start' => $registration->product->date_start,
            'product_date_end' => $registration->product->date_end,
            'place_name' => $registration->product->place ? $registration->product->place->name : null,
            'place_address' => $registration->product->place ? $registration->product->place->address : null,
            'place_city' => $registration->product->place ? $registration->product->place->city : null,
            'place_zip' => $registration->product->place ? $registration->product->place->zip : null,
            'place_state' => $registration->product->place ? $registration->product->place->state->name : null,
        ];
    }
}
