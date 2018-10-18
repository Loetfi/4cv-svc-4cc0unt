<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use App\User;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
    * @var Illuminate\Support\Facades\Auth;
    */
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
        $this->jwt  = $jwt;
    }

    /**
	* @param email string type email
	* @param password string
	* @return json response
    */
	public function login(Request $request)
	{
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            
            $user = \App\User::where('Email','=',$request->email);
            
            if($user->count() > 0) {
	            
                if(Hash::check($request->password, $user->first()->Password)) {
	                
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

            return response()->json(Api::format('0',[], $e->getMessage()), 500);

        }

        return response()->json(Api::format('1',['token_type'=>'Bearer','access_token'=>$token,'expires_in' => $this->guard()->factory()->getTTL() * 60],'Success'), 200);
    }

    /**
    * @param FullName string
    * @param Email unique
    * @param Password min 6, ConfirmPassword same Password
    * @param PhoneNumer numeric
    * @return json response
    */
    public function register(Request $request)
    {
        $this->validate($request, [
            'fullname'          => 'required|string',
            'email'             => 'required|email|unique:users,Email',
            'password'          => 'required|min:6',
            'confirm_password'  => 'required|same:password',
            'phone_number'      => 'required|numeric',
        ]);

        try {
            $data_user = [
                'FullName'      => $request->fullname,
                'Email'         => $request->email,
                'Password'      => $request->password,
                'PhoneNumer'    => $request->PhoneNumer
            ];

            $user = User::create($data_user);

            return response()->json(Api::format('1',['user'=>$user],'Success Register'), 200);

        } catch (\Exception $e) {
            
            return response()->json(Api::format('0',['message'=> $e->getMessage()],'Error'), 500);

        }
    }

    /**
    * Logout user (Validate token)
    * @return json response
    */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(Api::format('1',[],'Success logout'), 200);
    }

    /**
    * @param header Authorization Bearer token
    * @return json response
    */
    public function getUserByToken(Request $request)
    {
        try {
            
            $request->header('Authorization');
            
            if (! $user = $this->jwt->parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (\Exception $e) {

            return response()->json(Api::format('0',[], $e->getMessage()), 500);

        } 

        return response()->json(Api::format('1',['user'=>$user],'Success'),200);
    }

}