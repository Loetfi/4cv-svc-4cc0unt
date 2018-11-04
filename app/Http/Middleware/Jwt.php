<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Jwt
{
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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->header('Authorization');
            if(!$token) {
                throw new \Exception("Unauthorized", 401); 
            }

            $r =  (object) RestCurl::exec('GET',env('URL_SERVICE_ACCOUNT').'/auth/check-token',[],$token);
            
            if($r->data->status !== '1')
            {
                return response()->json(Api::format($r->data->status,$r->data->data,$r->data->message), 200);
            }   
        
        } catch (\Exception $e) {
            return response()->json(Api::format('0',[],$e->getMessage()), 500);
        }
        
        if(isset($r))
            $request->merge((array)$r->data->user);
        
        return $next($request);
    }
}
