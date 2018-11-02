<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->increments('UserId');
            $table->string('FullName');
            $table->string('Email')->nullable();
            $table->string('Password', 60)->nullable();
            $table->string('PhoneNumber')->nullable();
            $table->string('Provider')->nullable();
            $table->string('ProviderId')->nullable();
            $table->string('Avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
