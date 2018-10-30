<?php  

namespace App\Repositories;

use App\User as UserDB;
use Illuminate\Database\QueryException; 

class AuthRepo
{
	public static function SearchEmail($email)
	{
		try { 
			$res_email = $email ? $email : '';
			return $user = UserDB::where('Email','=',$res_email);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}

	} 
}
