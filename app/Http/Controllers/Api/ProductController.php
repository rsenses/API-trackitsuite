<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Place;
use App\Product;

class ProductController extends Controller
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
            'name' => 'required|max:255',
            'image_url' => 'required|url',
            'date_start' => 'required|date|after:today',
            'date_end' => 'required|date|after:date_start',
            'capacity' => 'required|integer',
            'company_id' => 'required|exists:company,company_id',
            'place_name' => 'nullable|max:255',
            'place_address' => 'required_with:place_name',
            'place_city' => 'required_with:place_name',
            'place_zip' => 'required_with:place_name',
            'state_id' => 'required_with:place_name|exists:state,state_id',
        ]);

        $place = Place::createOrUpdate($request);

        $product = new Product();

        $product->name = $request->name;
        $product->slug = str_slug($request->name);
        $product->image = $request->image_url;
        $product->description = $request->description;
        $product->date_start = $request->date_start;
        $product->date_end = $request->date_end;
        $product->capacity = $request->capacity;
        $product->place_id = $place ? $place->place_id : null;
        $product->company_id = $request->company_id;

        $product->save();

        return $product->with('place')->first();
    }
}
