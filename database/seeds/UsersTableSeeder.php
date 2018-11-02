<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
	    	[
	    		[
		            'FullName' 	=> 'admin',
		            'Email' 	=> 'admin@gmail.com',
		            'Password' 	=> app('hash')->make('secret'),
		            'PhoneNumber' => '89677899',
                    'Provider'  => 'manual'
		        ]
	        ]
    	);
    }
}
