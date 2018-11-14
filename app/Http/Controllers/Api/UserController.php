<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user()->with(['products' => function ($query) {
            $query->where('product_user.date_start', '<', Carbon::now())
                ->where('product_user.date_end', '>', Carbon::now());
        }])
            ->firstOrFail();

        return $user;
    }
}
