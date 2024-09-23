<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $remAddress = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'-';
        if(in_array($remAddress, ['182.185.135.64','124.29.217.76', '103.131.213.244','182.185.178.240','182.185.208.233','182.185.131.199','42.201.233.47','42.201.233.72','182.185.253.149','182.178.138.64'])) {
            config(['app.debug' => true]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
