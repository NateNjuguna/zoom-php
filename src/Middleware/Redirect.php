<?php
namespace Zoom\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Zoom\Auth;

class Redirect implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http\Request
     */
    public function handle(Request $request) {

        if ($request->hasRewrite) {
            return redirect($request->getRewriteUrl());
        }

    }
}
