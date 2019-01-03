<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Repositories\AuthRepo as AuthRepo;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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

            $user = AuthRepo::SearchEmail($request->email);
            
            if($user->count() > 0) {
                if(Hash::check($request->password, $user->first()->Password)) {
                    if( (int) $user->first()->IsActive !== 1 ) { 
                        
                        // user not active
                        return response()->json(Api::format('0',[],'Akun tidak aktif'), 400);
                    
                    } else if (! $token = $this->jwt->fromUser($user->first())) {
                        
                        // get token 
                        return response()->json(Api::format('0',[],'User tidak ditemukan'), 404);
                    
                    }
                } else {
                    
                    // password wrong
                    return response()->json(Api::format('0',[],'Email atau password salah'), 400);
                
                }
            } else {
                
                // email not register
                return response()->json(Api::format('0',[],'Email tidak terdaftar'), 400);           	
            
            }

        } catch (\Exception $e) {

            return response()->json(Api::format('0',[], $e->getMessage()), 500);

        }

        return response()->json(Api::format('1',['token_type'=>'Bearer','access_token'=>$token,
            'expires_in' => $this->guard()->factory()->getTTL() * 60],'Success'), 200);
    }

    /**
    * Logout user (Validate token)
    * @return json response
    */
    public function logout(Request $request)
    {
        /*
        * if error You must have the blacklist enabled to invalidate a token.
        * solution set false to true blacklist_enabled
        * tymon\jwt-auth\config\config.php 'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true).
        */
        $token = str_replace('Bearer', '', $request->header('Authorization'));
        $this->guard()->invalidate($token);

        return response()->json(Api::format('1',[],'Success logout'), 200);
    }

    /**
    * @param header Authorization Bearer token
    * @return json response
    */
    public function checkToken(Request $request)
    {
        try {

            $request->header('Authorization');

            if (! $user = $this->jwt->parseToken()->authenticate()) {
                return response()->json(Api::format('0',[],'Unauthorized'), 401);
            }
        } catch (TokenExpiredException  $e) {
            try {

                $refresh_token = $this->jwt->parseToken()->refresh();
                
                $request->header('Authorization', 'Bearer ' . $refresh_token);

            }
            catch (JWTException $e)
            {
                return response()->json(Api::format('0',[],$e->getMessage()), 500);    
            }

            $user_set_token = $this->jwt->setToken($refresh_token)->toUser();
            
            $this->guard()->login($user_set_token, false);
            
            return response()->json(Api::format('1',['token_type'=>'Bearer','expires_in' => $this->guard()->factory()->getTTL() * 60, 
                'refresh_token'=> $refresh_token],'refresh_token'),200);

        } catch (JWTException $e) {

            return response()->json(Api::format('0',[],$e->getMessage()), 500);

        }

        return response()->json(Api::format('1',$user,'valid_token'),200);
    }

    // check user ketika user menggunakan login omni channel / manual dan diketahui user sudah mempunyai akun
    public function checkUserProvider(Request $request)
    {
        try {
            $this->validate($request, [
                'email'          => 'required|email'
            ]);

            $param = [
                'Email' => $request->email
            ];

            $user = AuthRepo::UserByProvider($param);
            
            $status   = 1;
            $httpcode = 200;
            $data     = $user;
            $errorMsg = 'Success';

        }catch(\Exception $e){
            $status   = 0;
            $httpcode = 400;
            $data     = null;
            $errorMsg = $e->getMessage();
        }
        return response()->json(Api::format($status, $data, $errorMsg), $httpcode); 
    }
}
