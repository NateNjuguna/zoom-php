<?php
namespace Zoom;

use DI\ContainerBuilder;

final class Container {
    /**
     * Hold the class instance
     * 
     * @var \DI\Container
     */
    private static $__instance;
    
    /**
     * Create a new container object
     * 
     * @return  void
     */
    private function __construct() {
        // Expensive stuff
    }
   
    /**
     * Get the instance of the container
     * 
     * @return  \DI\Container
     */
    public static function getInstance() {
      if ( empty(static::$__instance) ) {
        static::$__instance = static::_makeContainer();
      }
      return static::$__instance;
    }
   
    /**
     * Set the instance of the container
     * 
     * @param  \DI\Container    $container
     * @return  void
     */
    /**
     * Load environment variables
     * 
     * @return  \DI\Container
     */
    protected static function _makeContainer() {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        return $builder->build();
    }
}
