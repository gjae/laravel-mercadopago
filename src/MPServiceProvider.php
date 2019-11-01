<?php
namespace Gjae\MercadoPago;

use Gjae\MercadoPago\MercadoPago;

use Illuminate\Support\ServiceProvider;
class MPServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
    }

    public function register()
    {
        $this->app->singleton('MercadoPago', function($app){
            return new MercadoPago(config('mercadopago'), config('mercadopago.'.config('mercadopago.mode') ) );
        });

        $this->app->bind(
            'Gjae\MercadoPago\Contracts\MPResponse',
            'Gjae\MercadoPago\Classes\MercadoPagoResponse'
        );
    }


    /**
     * Copia el archivo de configuracion
     *
     * @return void
     */
    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../mercadopago.php'   => config_path('mercadopago.php'),
        ], 'config'); 
    }

    /**
     * Copia las migraciones
     *
     * @return void
     */
    public function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../migrations'         => database_path('migrations')
        ], 'migrations');
    }
}