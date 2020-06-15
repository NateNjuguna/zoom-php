<?php
namespace Zoom\Routing;

use Zoom\Exceptions\Exception;
use Zoom\Middleware\MiddlewareNotFoundException;
use Zoom\Middleware\MiddlewareNotRegisteredException;

abstract class Controller {

    /**
     * Checks if a request satisfies route middleware options
     * 
     * @param   array   $options
     * @return  boolean
     * 
     * @throws  \Zoom\Middleware\MiddlewareNotFoundException
     */
    private function _satisfiesMiddlewareOptions($options) {
        $satisfactory = true;
        $request_url = request()->getUrl()->getPath();
        $controller_class_name = '\\' . get_class($this);
        $relative_class_name = str_replace(Router::getDefaultNamespace() . '\\', '', $controller_class_name);
        if (isset($options['except'])) {
            $methods = $options['except'];
            if (is_string($methods)) {
                $methods = [$methods];
            }
            foreach ($methods as $method) {
                $method_url = url("{$relative_class_name}@{$method}", input()->all());
                if ($request_url === $method_url) {
                    $satisfactory = false;
                }
            }
        }
        if (isset($options['only'])) {
            $satisfactory = false;
            $methods = $options['only'];
            if (is_string($methods)) {
                $methods = [$methods];
            }
            foreach ($methods as $method) {
                $method_url = url("{$relative_class_name}@{$method}", input()->all());
                if ($request_url === $method_url) {
                    $satisfactory = true;
                }
            }
        }
        return $satisfactory;
    }
    
    /**
     * Load route specific middleware
     * 
     * @param   string  $middleware
     * @param   array   $options
     * @return  void
     * @throws  \Zoom\Middleware\MiddlewareNotFoundException
     * @throws  \Zoom\Middleware\MiddlewareNotRegisteredException
     */
    protected function middleware($middleware, $options = []) {
        $middlewares = app()->get('route.middleware');
        if (isset($middlewares[$middleware])) {
            $class_name = $middlewares[$middleware];
            if (class_exists($class_name)) {
                if ($this->_satisfiesMiddlewareOptions($options)) {
                    $class = new $class_name;
                    $class->handle(request());
                }
            } else {
                throw new MiddlewareNotFoundException("'{$class_name}' could not be found.");
            }
        } else {
            throw new MiddlewareNotRegisteredException("Route Middleware '{$middleware}' has not been registered.");
        }
    }
}
