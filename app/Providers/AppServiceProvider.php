<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('company', function ($attribute, $value, $parameters, $validator) {
            if (request()->user()->is_admin) {
                return true;
            }

            return User::where('user_id', request()->user()->user_id)
                ->whereHas('companies', function ($query) use ($value) {
                    $query->where('company_user.company_id', $value);
                })->exists();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
