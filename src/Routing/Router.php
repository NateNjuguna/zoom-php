<?php
namespace Zoom\Routing;

use Pecee\Http\Url;
use Pecee\SimpleRouter\SimpleRouter;
use Zoom\FS;

class Router extends SimpleRouter {

    /**
     * Add user defined routes
     * 
     * @return void
     */
    protected static function _addRoutes() {
        parent::group(
            [
                'exceptionHandler' => static::_appNamespace('\Exceptions\Handler'),
                'middleware' => static::_appNamespace('\Middleware\VerifyCsrfToken'),
            ],
            function() {
                require FS::disk('app')->path('routes.php');
            }
        );
    }

    /**
     * Add user defined routes
     * 
     * @param  string  $namespace
     * @return string
     */
    protected static function _appNamespace($class = '') {
        return app()->namespace . $class;
    }

    /**
     * Start processing the routes
     * 
     * @return void
     */
    public static function start() {
        /**
         * The default namespace for route-callbacks, so we don't have to specify it each time.
         * Can be overwritten by using the namespace config option on your routes.
         */
        parent::setDefaultNamespace(static::_appNamespace('\Controllers'));
        // Load application routes
        $app_url = new Url(config('app.url'));
        $path = $app_url->getPath();
        if (!in_array($path, ['/', ''])) {
            parent::group(['prefix' => $path], static::_addRoutes);
        } else {
            static::_addRoutes();
        }
        parent::start();
    }

}
