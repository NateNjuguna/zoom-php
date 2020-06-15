<?php
namespace App\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Zoom\Auth;

class Authenticate implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http\Request|\Pecee\Http\Response
     */
    public function handle(Request $request) {

        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
        } else {
            cookie('return', $request->getUrl());
            return redirect(url('AuthController@getLogin'));
        }
        return $request;

    }
}
