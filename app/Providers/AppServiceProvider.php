<?php

namespace App\Providers;

use URL;
use Config;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        URL::forceSchema('https');
        setlocale(LC_ALL, Config::get('app.lc_all'));
        Carbon::setLocale(Config::get('app.locale'));
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
