<?php  

namespace App\Repositories;

use App\User as UserDB;
use Illuminate\Database\QueryException; 

class UserRepo
{
	public static function SearchEmail($email)
	{
		try { 
			return UserDB::where('Email','=',$email)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}

	}

	public static function UpdateByEmail($email,$data)
	{
		try {
			return UserDB::where('Email',$email)->update($data);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage(), 500);
			
		}
	} 
}
