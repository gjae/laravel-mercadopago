<?php
namespace Gjae\MercadoPago;

use Gjae\MercadoPago\MercadoPago;

use Illuminate\Support\ServiceProvider;
class MPServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('MercadoPago', function($app){
            return new MercadoPago(config('mercadopago'), config('mercadopago.credentials') );
        });
    }


    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../mercadopago.php'   => config_path('mercadopago.php'),
        ], 'config'); 
    }
}