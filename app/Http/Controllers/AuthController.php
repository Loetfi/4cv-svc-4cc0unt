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
                        return response()->json(Api::format('0',[],'Your account is not active'), 400);
                    
                    } else if (! $token = $this->jwt->fromUser($user->first())) {
                        
                        // get token 
                        return response()->json(Api::format('0',[],'User not found'), 404);
                    
                    }
                } else {
                    
                    // password wrong
                    return response()->json(Api::format('0',[],'Your email or password wrong'), 400);
                
                }
            } else {
                
                // email not register
                return response()->json(Api::format('0',[],'Your email not registered'), 400);           	
            
            }

    } catch (\Exception $e) {

        return response()->json(Api::format('0',[], $e->getMessage()), 500);

    }

    return response()->json(Api::format('1',['token_type'=>'Bearer','access_token'=>$token,
        'expires_in' => $this->guard()->factory()->getTTL() * 60],'Success'), 200);
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
            'phone_number'      => 'numeric',
        ]);

        try {
            $data_user = [
                'FullName'      => $request->fullname,
                'Email'         => $request->email,
                'Password'      => Hash::make($request->password),
                'PhoneNumer'    => $request->phone_number ? $request->phone_number : null,
                'Provider'      => $request->provider ? $request->provider : null,
                'ProviderId'    => $request->provider_id ? $request->provider_id : null,
                'Avatar'        => $request->avatar ? $request->avatar : null,
                'IsActive'      => $request->is_active ? $request->is_active : 0,
            ];

            $user = AuthRepo::RegisterUser($data_user);

            return response()->json(Api::format('1',$user,'Success Register'), 200);

        } catch (\Exception $e) {

            return response()->json(Api::format('0',[],$e->getMessage()), 500);

        }
    }

    /**
    * Logout user (Validate token)
    * @return json response
    */
    public function logout()
    {
        $this->guard()->logout();
        // print_r($a);die();
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
