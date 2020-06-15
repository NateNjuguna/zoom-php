<?php
namespace Zoom;

use Zoom\Database\Database;

class Auth {
    
    /**
     * Authenicate a user
     * 
     * @param   \Std    $user
     * @param   boolean $remember
     * @return  void
     */
    public static function authenticate($user, $remember = false) {
        session(config('auth.session'), $user->id);
        if( $remember && isset($user->{config('auth.remember')}) ) {
            cookie('cmVtZW1iZXI', $user->{config('auth.remember')}, 30);
        }        
    }

    
    /**
     * Check if the user is authenticated
     * 
     * @return  void
     */
    public static function check() {
        return session()->has(config('auth.session'));
    }
    
    /**
     * Log out the authenticated user
     * 
     * @return  void
     */
    public static function deauthenticate() {
        if(isset($_COOKIE['cmVtZW1iZXI'])) {
            cookie('cmVtZW1iZXI', '', -7);
        }
        session()->flush();
    }

    /**
     * Create a valid password
     * 
     * @param   string  $string
     * @return  string
     */
    public static function password($str) {
        return hash_hmac('sha256', $str, config('auth.secret'));
    }

    /**
     * Remember a user
     * 
     * @return  void
     */
    public static function remember() {
        if ( !static::check() && !is_null(cookie('cmVtZW1iZXI')) ) {
            $remember_token = cookie('cmVtZW1iZXI');
            $user = Database::table(config('auth.table'))->where(config('auth.remember'), $remember_token)->first();
            if ( is_object($user) ) {
                static::authenticate($user);
            } else {
                static::deauthenticate();
            }
        }
    }

    /**
     * Get the authenticated user
     * 
     * @return \stdClass
     */
    public static function user() {
        return Database::table(config('auth.table'))->find(session(config('auth.session')) + 0);
    }

}