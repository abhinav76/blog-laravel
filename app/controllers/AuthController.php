<?php

class AuthController extends \BaseController {


	/**
	 * Login user with facebook
	 *
	 * @return void
	 */

	public function loginWithFacebook() {

	    // get data from input
	    $code = Input::get( 'code' );

	    // get fb service
	    $fb = OAuth::consumer( 'Facebook' );

	    // check if code is valid

	    // if code is provided get user data and sign in
	    if ( !empty( $code ) ) {

	        // This was a callback request from facebook, get the token
	        $token = $fb->requestAccessToken( $code );

	        // Send a request with it
	        $result = json_decode( $fb->request( '/me' ), true );

	        $message = 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
	        echo $message. "<br/>";

	        //Var_dump
	        //display whole array().
	        dd($result);

	    }
	    // if not ask for permission first
	    else {
	        // get fb authorization
	        $url = $fb->getAuthorizationUri();

	        // return to facebook login url
	         return Redirect::to( (string)$url );
	    }

	}

	/**
	 * Show the form for creating a new resource.
	 * GET /admin
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('admin.index');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /admin
	 *
	 * @return Response
	 */
	public function store()
	{  
	  	if ($this->isPostRequest()) {
      		$validator = $this->getLoginValidator();
  
	      	if ($validator->passes()) {
	       		$credentials = $this->getLoginCredentials();
	  
		        if (Auth::attempt($credentials)) {
		          	return Redirect::to("/");
		        }
	  
		        return Redirect::back()->withErrors([
		          "invalid_credential" => ["Credentials invalid."]
		        ]);
	      	} else {
	        	return Redirect::back()
		          ->withInput()
		          ->withErrors($validator);
	      	}
    	}
  
   		return View::make("admin.index");
	}

	/**
	 * Logout
	 *
	 * @return Response
	 * @author 
	 **/
	public function logout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

	//Check user's post request
	protected function isPostRequest()
  	{
    	return Input::server("REQUEST_METHOD") == "POST";
  	}
  
  	//Validate
  	protected function getLoginValidator()
  	{
	    return Validator::make(Input::all(), [
	      "email" => "required|email",
	      "password" => "required"
	    ]);
  	}

  	//Get Login Credentials
	protected function getLoginCredentials()
  	{
	    return [
	      "email" => Input::get("email"),
	      "password" => Input::get("password")
	    ];
  	}
}