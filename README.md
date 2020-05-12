## Instalación

Ejecute el siguiente comando

```php 
composer require gjae/laravel-mercadopago
```

## Configuración básica

en su archivo ``` config/app.php ``` agregue las siguientes lineas (SOLO PARA VERSIONES DE LARAVEL <= 5.4):

en su arreglo de proveedores de servicio ( providers ):
```php 
Gjae\MercadoPago\MPServiceProvider::class, 
```

agregue el siguiente facade a su lista de aliases (SOLO PARA VERSIONES DE LARAVEL <= 5.4):
```php
'MercadoPago' => Gjae\MercadoPago\Facade::class,
```
por ultimo ejecute el siguiente comando:
```php
php artisan vendor:publish --provider="Gjae\MercadoPago\MPServiceProvider"
```

Y por ultimo ejecutar las migraciones:

```php
php artisan migrate
```

Ahora vaya al archivo ``` config/mercadopago.php ``` y agregue su configuración, su archivo se vera similar a esto: 

```php
<?php

return [

    /**
     * Si es verdadero entonces no se validan las credenciales
     * en mercadopago
     */
    'local_debug'        => true,

    /**
     * Especifica el modo en el que se estara usando
     * el mercado pago, : sandbox, production
     */
    'mode'              => 'sandbox',

    /**
     * Credenciales para el caso en que la aplicacion este en modo 
     * produccion (especificado en la clave mode)
     */
    'production'        => [

        'access_token'  => env('MP_ACCESS_TOKEN', ''),


        'public_key'    => env('MP_PUBLIC_KEY', ''),


    ],

    /**
     * Credenciales para el modo sandbox
     * especificado el uso en la clave "mode"
     */
    'sandbox'           => [
        'access_token'  => env('MP_SANDBOX_ACCESS_TOKEN', ''),

        'public_key'    => env('MP_SANDBOX_PUBLIC_KEY', '')
    ],


    /**
     * Tipo de identificacion usada para los pagos 
     */
    'identification_type'   => 'DNI',


    /**
     * Codigo de area telefonico
     */
    'area_code'             => '',

    /**
     * Especifica las URL de retorno para el smartcheckout
     */
    'back_urls'             => [

        'success'   => '',

        'failure'   => '',

        'pending'   => '',

    ],

    // Indica en que caso de respuesta del pago se ejecutara una autorecarga
    // Por defecto: approved (el usuario pagador volvera automaticamente en caso de que el pago haya sido completado y aprobado)
    'auto_return'   => 'approved',


];
```

## Uso básico

Para comenzar a utilizar la librería unicamente necesita llamar al facade MercadoPago (o como le haya nombrado en el arreglo aliases de su archivo ```config.php``` ), llamando al metodo begin el cual tiene como parametro una función callback que recibira como parametro la transacción propiamente, ejemplo:

```php
\MercadoPago::begin(function($mp){
    // agrega un item al procesamiento
    $mp->addItem([
        'title' => 'Prueba', // Titulo del item
        'qtty'  => 1,        // Cantidad del item
        'price' => 150.0, // Precio unitario
        'currency' => 'USD', // MONEDA USADA PARA PAGAR
        'id'    =>  "MYAWESOMEPRODUCTID" // ID DEL PRODUCTO (PARA CONTROL INTERNO DE SU APLICACIÓN)
    ]);

    // OPCIONAL: el metodo backUrlAddQS agregara parametros adicionales a la URL de pago, dichos parametros seran devueltos al completar la transaccipon
    // usado para control interno de su propia aplicación, si desea agregar un token o ID de seguridad a su proceso
    $mp->backUrlAddQS([ 'foo' => "bar" ]);

});
```

El metodo addItem puede ser llamado las veces que considere necesarias para agregar los items que necesite cargar al pago. Por ultimo, en su vista de respuesta  
tendra disponible el metodo initPoint del facade MercadoPago (ejemplo de codigo de la vista):

```HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Redirigiendo a MercadoPago para procesar su compra</title>
</head>
<body>
    
<input type="hidden" name="redirect-mp" id="mp" value="{{ \MercadoPago::initPoint() }}">

<script>

window.location = document.getElementById('mp').value

</script>

</body>
</html>
```
las respuestas que retorne la pasarela de pago deben ser controlada desde sus rutas y agregadas en el archivo de configuración (```config/mercadopago.php```).

## Procesando respuesta

En las rutas asociadas a la respuesta (configuradas en el archivo de configuración), llame a la clase MPResponse y use inyección de dependencias para procesar la respuesta, ejemplo: 

```php
<?php
...
use Gjae\MercadoPago\Contracts\MPResponse;

class MercadoPagoController extends Controller
{
    public function successResponse(MPResponse $request)
    {
        ...
    }

}

```

el objeto MPResponse inyectado, automaticamente guardara los datos de la respuesta por usted.

## Asociar objetos con el pago

Adicionalmente puede asociar modelos con su transacción, para esto debe ir a su clase modelo Eloquent e implementar la interfaz HasTransaction y el trait HasTransactions incluidos en el paquete:

```php
namespace App;

...
use Illuminate\Database\Eloquent\Model;
use Gjae\MercadoPago\Contracts\HasTransaction;
use Gjae\MercadoPago\Traits\HasTransactions;

class MyAwesomeModel extends Model implements HasTransaction{
    use HasTransactions;
    ...
}
```
Habiendo realizado esto, puede pasar como segundo parametro de la funcion begin, un objeto (o array de objetos) de cualquier clase que implemente la interfaz HasTransaction:
```php
\MercadoPago::begin(function($mp){
    ...
}, [ $HasTransactionObjects ]);
```

### Obteniendo los objetos relacionados
Los objetos que implementan la interfaz ```HasTransaction``` y el trait ```HasTransactions``` cuentan la extensión transactions que retorna una colección de transacciones relacionadas a dicho objeto:

```php

$user = App\User::first();

$user->transactions; 

```

### Notas de observación
El objeto MPResponse que se inyecta al controlador cuenta con el metodo getTransaction que retorna los datos de la transacción recibida

```php
public function successResponse(MPResponse $request)
{
    $transaction = $request->getTransaction();
}
```

## Nota:
Cuando el archivo de configuración tenga en true la opcion "local_debug"; la no se emitira un init_point con la URL de mercadopago, esto es para que el usuario no cree  transacciones que no se realizaran dentro de la pasarela y pueda probar tranquilamente el funcionamiento sin esperar respuestas del servidor de mercadopago sin necesidad, cuando este listo para probar el funcionamiento completo bien sea en producción o en modo sandbox, cambie esta opcion a false

### ToDo
- [ ] Emitir excepción cuando se encuentre la aplicación en modo debug local
