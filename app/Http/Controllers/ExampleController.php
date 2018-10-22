<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Helpers\RestCurl;
use App\Helpers\Api;
use Tymon\JWTAuth\JWTAuth;

class ExampleController extends Controller
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

    public function index()
    {
        return RestCurl::exec('GET','http://localhost/acv-dev/4cv-svc-4cc0unt/public/',[],'');
    }

    public function login(Request $request) 
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        // echo "<pre>";
        // print_r(\App\User::where('Email','=',$request->input('email'))->first());die();

        try {
            $user = \App\User::where('Email','=',$request->input('email'))->first();
            if(Hash::check($request->input('password'), $user->Password)) {
                if (! $token = $this->jwt->fromUser($user)) {
                    return response()->json(Api::format('1',[],'User not found'), 404);
                }
            } else {
                return response()->json(Api::format('0',[],'Email or password wrong.!'), 400);
            }

        } catch (\Exception $e) {

            return response()->json(['message'=> $e->getMessage()], 500);

        }

        return response()->json(Api::format('1',['type'=>'Bearer','access_token'=>$token],'Success'), 200);
    }

    public function me(Request $request)
    {
        print_r($request->FullName);
    }

    public function logout()
    {

    }
}
