<?php
namespace App\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\Http\Response;
use Zoom\Auth;

class RedirectIfAuthenticated implements IMiddleware {

    /**
     * Redirect the user if they are already authenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http\Request|\Pecee\Http\Response
     */
    public function handle(Request $request) {

        Auth::remember();

        if (Auth::check()) {
            if (cookie('return')) {
                $url = cookie('return');
                cookie('return', '', -7);
                return redirect($url);
            }
            return redirect(url('IndexController@index'));
        } else {
            // Check for a locale set and set the locale
            if (in_array($request->getMethod(), ['get', 'GET']) && input('locale', false)) {
                config('app.locale.default', input('locale'));
            }
        }
        return $request;

    }
}
