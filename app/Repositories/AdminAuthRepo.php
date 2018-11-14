<?php  

namespace App\Repositories;

use App\AdminUser as AdminUserDB;
use Illuminate\Database\QueryException; 

class AdminAuthRepo
{
	public static function SearchEmail($email)
	{
		try { 
			$res_email = $email ? $email : '';
			return AdminUserDB::where('Email','=',$res_email);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}

	}

	public static function RegisterUser($data)
	{
		try {
			return AdminUserDB::create($data);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage(), 500);
			
		}
	}

	// public static function UserByProvider($param)
	// {
	// 	try {
	// 		return AdminUserDB::where($param)->first();
	// 	} catch (QueryException $e) {
	// 		throw new \Exception($e->getMessage(), 500);
	// 	}
	// } 
}
