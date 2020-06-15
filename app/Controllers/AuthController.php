<?php
namespace App\Controllers;

use Zoom\Auth;
use Zoom\Database\Database;
use Zoom\Middleware\Redirect;
use Zoom\Routing\Controller;

class AuthController extends Controller {

    /**
     * Create a new Controller instance
     * 
     * @return  void
     */
    public function __construct() {
        $this->middleware('auth', ['only' => 'getLogout']);
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function getForgot() {
        return view('auth.forgot-password');
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function getLogin() {
        return view('auth.login');
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @param   string  $token  The reset token sent to the user's email
     * @return  \Pecee\Http\Response
     */
    public function getReset($token) {
        return view('auth.reset', compact('token'));
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function getLogout() {
        Auth::deauthenticate();
        return redirect(url('AuthController@getLogin'));
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function postForgot() {
        // Send reset link email to user
        response()->header('Content-Type: text/plain');
        return 'Reset link sent';
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function postLogin() {
        $email = input('email');
        $password = input('password');
        $user = Database::table(config('auth.table'))->where('email', $email)->first();
        if (!is_null($user)) {
            if(hash_compare($user->password, Auth::password($password))){
                Auth::authenticate($user, input('remember_me', false));
                $redirect_url = cookie('return') ?: url('IndexController@index');
                redirect($redirect_url);
            }
        }

        response()->headers([
            'HTTP/1.1 400 Bad Request',
            'Content-Type: text/plain',
        ]);
        return 'Wrong username or password';
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return  \Pecee\Http\Response
     */
    public function postReset() {
        // Verify token and post
        response()->header('Content-Type: text/plain');
        return 'Password reset successfully';
    }

}
