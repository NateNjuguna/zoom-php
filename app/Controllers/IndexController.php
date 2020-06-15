<?php
namespace App\Controllers;

use Zoom\Auth;
use Zoom\Routing\Controller;

class IndexController extends Controller {
    
    /**
     * Create a new Controller instance
     * 
     * @return  void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'index']);
    }

    /**
     * Get a sample view or redirect to it
     * 
     * @return \Pecee\Http\Response
     */
    public function index() {
        return view('app.index');
    }

    /**
     * Get a 404 view
     * 
     * @return \Pecee\Http\Response
     */
    public function error404() {
        return view('errors/404');
    }

}
