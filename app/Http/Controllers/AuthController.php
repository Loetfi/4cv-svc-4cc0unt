<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    private function guard()
    {
        return Auth::guard();
    }

	/**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
	* @param email string type email
	* @param password string
	* return json
    */
	public function login(Request $request)
	{
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = \App\User::where('Email','=',$request->input('email'));
            if($user->count() > 0) {
	            if(Hash::check($request->input('password'), $user->first()->Password)) {
	                if (! $token = $this->jwt->fromUser($user->first())) {
	                    return response()->json(Api::format('1',[],'User not found'), 404);
	                }
	            } else {
	                return response()->json(Api::format('0',[],'Your password wrong'), 400);
	            }
            } else {
				 return response()->json(Api::format('0',[],'Your email not registered'), 400);           	
            }

        } catch (\Exception $e) {

            return response()->json(Api::format('0',['message'=> $e->getMessage()],'Error'), 500);

        }
        return response()->json(Api::format('1',['token_type'=>'Bearer','access_token'=>$token,'expires_in' => Auth::guard()->factory()->getTTL() * 60],'Success'), 200);
    }

    public function register(Request $request)
    {

    }

    public function logout()
    {

    }

    public function check(Request $request)
    {
        $data = $request->header('Authorization');
        print_r($request->input('acces_token'));die();
        try {

            if (! $user = $this->jwt->parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

}