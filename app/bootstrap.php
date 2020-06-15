<?php

use Zoom\Application;

// Set this to true to turn on development features.
$debug = true;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Zoom application instance
| which serves as the "glue" for all the components of Zoom, and is
| the IoC container for the system binding all of the various parts.
|
*/
$app = new Application($debug);
$app->namespace = config('app.namespace');

/*
|--------------------------------------------------------------------------
| Register Important Services
|--------------------------------------------------------------------------
|
| Next, we need to bind some important services into the container so
| we will be able to resolve them when needed.
|
*/
$app->register('route.middleware', [
    'auth'  => App\Middleware\Authenticate::class,
    'guest' => App\Middleware\RedirectIfAuthenticated::class,
]);

if(! function_exists('app')) {
    /**
     * Obtain the application instance
     * 
     * @return  \Zoom\Application
     */
    function app() {
        global $app;
        return $app;
    }
}

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;