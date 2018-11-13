<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('useradmin')->insert(
	    	[
	    		[
		            'FullName' 	=> 'Admin ACV',
		            'Email' 	=> 'admin@gmail.com',
		            'Password' 	=> app('hash')->make('secret'),
		            'BranchId' => '2',
                    'Role' => '1'
		        ]
	        ]
    	);
    }
}
