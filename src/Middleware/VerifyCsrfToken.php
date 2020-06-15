<?php
namespace Zoom\Middleware;

use Pecee\Http\Middleware\BaseCsrfVerifier;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Zoom\Routing\Router;

class VerifyCsrfToken implements IMiddleware {

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http\Request
     */
    public function handle(Request $request) {
        /**
         * ,------,
         * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
         * '------' should be passed in a hidden field named "csrf-token" or a header
         *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
         */
        if (!in_array($request->getUrl()->getPath(), $this->except)) {
            $CSRF_verifier = new BaseCsrfVerifier();
            $CSRF_verifier->setTokenProvider(new SessionTokenProvider());
            Router::csrfVerifier($CSRF_verifier);
        }
        return $request;

    }
}
