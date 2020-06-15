<?php

use Zoom\Auth;
use Zoom\Container;
use Zoom\FS;
use Zoom\Config;
use Zoom\Session;
use Zoom\Str;
use Zoom\View\View;

if(! function_exists('asset')) {
    /**
     * Generate a valid asset url
     * 
     * @param   string  $url
     * @return  mixed
     */
    function asset($url) {
        return substr(url($url), 0, -1);
    }
}

if(! function_exists('auth')) {
    /**
     * Generate a valid asset url
     * 
     * @return  \Zoom\Auth
     */
    function auth() {
        return new Auth;
    }
}


if(! function_exists('back')) {
    /**
     * Redirect to the previous page
     * 
     * @return  void
     */
    function back() {
        return response()->redirect(request()->getReferrer());
    }
}


if(! function_exists('config')) {
    /**
     * Get a config value
     * 
     * @param   string  $str
     * @param   mixed   $value
     * @return  mixed
     */
    function config($str, $value = null) {
        if (is_null($value)) {
            return Config::get($str);
        }else {
            return Config::set($str, $value);
        }
        
    }
}

if(! function_exists('container')) {
    /**
     * Get/Set a config value
     * 
     * @param   string  $key
     * @param   mixed   $value
     * @return  mixed
     */
    function container($key, $value = null) {
        $container = Container::getInstance($key);
        if ( is_null($value) ) {
            return $container->get($key);
        } else {
            return $container->set($key, $value);
        }
    }
}

if (! function_exists('cookie')) {
    /**
     * Get/Set a cookie
     * 
     * @param   string  $key
     * @param   mixed   $value
     * @param   float   $days
     * @return  mixed
     */
    function cookie($key, $value = null, $days = 1) {
        if ( is_null($value) ) {
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        } else {
            return setcookie($key, $value, time() + (86400 * $days), '/');
        }
    }
}

if (! function_exists('csrf_field')) {
    /**
     * Generate a hidden HTML5 input for CSRF
     * 
     * @return  string
     */
    function csrf_field() {
        return '<input type="hidden" name="csrf-token" value="' . csrf_token() . '" />';
    }
}

if (! function_exists('disk_path')) {
    /**
     * Get the current session
     * 
     * @param   string  $disk
     * @param   string  $name
     * @return  string
     */
    function disk_path($disk = 'storage', $name = '/') {
        return FS::disk($disk)->path($name);
    }
}

if (! function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param  string  $value
     * @return string
     */
    function e($value) {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null) {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch ( strtolower($value) ) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if ( strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"') ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('hash_compare')) {
    /**
     * Compare two string hashes
     * 
     * @param   string  $a
     * @param   string  $a
     * @return  boolean
     */
    function hash_compare($a, $b) {
        if ( !is_string($a) || !is_string($b) ) { 
            return false; 
        } 
        
        $len = strlen($a); 
        if ($len !== strlen($b)) { 
            return false; 
        } 

        $status = 0; 
        for ($i = 0; $i < $len; $i++) { 
            $status |= ord($a[$i]) ^ ord($b[$i]); 
        } 
        return $status === 0; 
    }
}

if (! function_exists('session')) {
    /**
     * Get the current session
     * 
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  mixed
     */
    function session($key = null, $value = null) {
        $session = container(Session::class);
        if ( is_null($key) ) {
            return $session;
        } else if ( is_null($value) ) {
            return $session->get($key);
        } else {
            $session->put($key, $value);
        }        
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists('view')) {
    /**
     * Return a html page view
     * 
     * @param   string  $name
     * @param   array   $data
     * @return  string
     */
    function view($name = 'errors.404', array $data = [], array $sections = [], array $ext_pref = ['php', 'html', 'txt']) {
        $view = new View($name, $data, $sections, $ext_pref);
        return $view->compile()->render();
    }
}

if (! function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed    $object The object to be returned
     * @return mixed
     */
    function with($object) {
        return $object;
    }
}


if (! function_exists('__')) {
    /**
     * Get the translated value of the set language
     * 
     * @param   string  $name
     * @param   array   $data
     * @return  string
     */
    function __($name, array $data = []) {
        $dot_keys = explode('.', $name);
        $locale = config('app.locale.default');
        $PHP_path = config("filesystem.disks.language") . FS::OSCorrectPath("/{$locale}/{$dot_keys[0]}.php");
        if (file_exists($PHP_path)) {
            $value = include $PHP_path;
            if(count($dot_keys) > 1) {
                for($x = 1; $x < count($dot_keys); $x++) {
                    $value = $value[$dot_keys[$x]];
                }
            }
            if(count($data) > 0) {
                $value = str_replace(
                    array_map(function($key) {
                        return ":{$key}";
                    }, array_keys($data)),
                    array_values($data),
                    $value
                );
            }
            return $value;
        } else {
            return $name;
        }
    }
}
