<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Repositories\AuthRepo;
use App\Repositories\UserRepo;

/**
* 
*/
class RegisterController extends Controller
{
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

    public function activeAccount(Request $request)
    {
    	try {
    		$data_user = [
    			'IsActive'	=> $request->is_active
    		];

    		UserRepo::UpdateByEmail($request->email,$data_user);

    		return response()->json(Api::format('1',[],'Your account success to activated'), 200);
    	} catch (\Exception $e) {
	        return response()->json(Api::format('0',[],$e->getMessage()), 500);
    	}
    }	
}