<?php

namespace Zoom\Middleware;

use Pecee\Http\Security\Exceptions\SecurityException;
use Pecee\Http\Security\ITokenProvider;
use Zoom\Session;

class SessionTokenProvider implements ITokenProvider {
    const CSRF_KEY = 'CSRF-TOKEN';

    protected $token;

    /**
     * CookieTokenProvider constructor.
     * 
     * @throws Pecee\Http\Security\Exceptions\SecurityException
     */
    public function __construct() {
        $this->token = $this->getToken();

        if ($this->token === null) {
            $this->token = $this->generateToken();
        }
    }

    /**
     * Generate random identifier for CSRF token
     *
     * @return string
     * 
     * @throws Pecee\Http\Security\Exceptions\SecurityException
     */
    public function generateToken() {
        if (function_exists('random_bytes') === true) {
            try {
                return bin2hex(random_bytes(32));
            } catch(\Exception $e) {
                throw new SecurityException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
            }
        }

        $isSourceStrong = false;

        $random = openssl_random_pseudo_bytes(32, $isSourceStrong);
        if ($isSourceStrong === false || $random === false) {
            throw new SecurityException('IV generation failed');
        }

        return $random;
    }

    /**
     * Validate valid CSRF token
     *
     * @param  string  $token
     * @return bool
     */
    public function validate($token) {
        if ($token !== null && $this->getToken() !== null) {
            return hash_equals($token, $this->getToken());
        }

        return false;
    }

    /**
     * Set csrf token cookie
     * Overwrite this method to save the token to another storage like session etc.
     *
     * @param  string  $token
     * @return void
     */
    protected function setToken($token) {
        $this->token = $token;
        session()->put(static::CSRF_KEY, $token);
    }

    /**
     * Get csrf token
     * 
     * @param  string|null  $defaultValue
     * @return string|null
     */
    public function getToken($defaultValue = null) {
        $this->token = ($this->hasToken() === true) ? session()->get(static::CSRF_KEY) : null;
        
        return ($this->token !== null) ? $this->token : $defaultValue;
    }

    /**
     * Refresh existing token
     * 
     * @return void
     */
    public function refresh() {
        if ($this->token !== null) {
            $this->setToken($this->token);
        }
    }

    /**
     * Returns whether the csrf token has been defined
     * 
     * @return bool
     */
    protected function hasToken() {
        return session()->has(static::CSRF_KEY);
    }

}