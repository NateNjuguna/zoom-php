<?php
namespace Zoom;

class Config {

    /**
     * Cache all config files and return a combined array
     * 
     * @return  void
     */
    public static function cache() {
        $dir = str_replace("src", "config", __DIR__);
        $config = [];
        foreach(array_diff(scandir($dir), ['.', '..']) as $filename) {
            $config[str_replace('.php', '', $filename)] = include FS::OSCorrectPath("{$dir}/{$filename}");
        }
        return $config;
    }

    /** 
     * Get a config's value by reference
     * 
     * @param   array   $config The array to extract values from
     * @param   array   $keys   The nested key array whose value is to be extracted from $config
     * @return  mixed
     */
    protected static function &extractValueByReference( array &$config, array $keys ) {
        if ( count($keys) > 1 ) {
            $first = array_splice($keys, 0, 1)[0];
            return static::extractValueByReference($config[$first], $keys);
        }
        return $config[$keys[0]];
    }
    
    /**
     * Get a configuration
     * 
     * This method obtains configuration values from the application's container
     * 
     * @param   string  $key    A dot key syntax string of the config value wanted
     * @return  mixed
     */
    public static function get($key) {
        $dot_keys = explode('.', $key);
        $config = container('config');
        foreach($dot_keys as $dot_key) {
            $config = $config[$dot_key];
        }
        return $config;
    }

    
    /**
     * Set a configuration at runtime
     * 
     * @param   string $key
     * @return  void
     */
    public static function set($key, $value) {
        $config = container('config');
        $dot_keys = explode('.', $key);
        $branch = &static::extractValueByReference($config, $dot_keys);
        $branch = $value;
        container('config', $config);
    }
}
