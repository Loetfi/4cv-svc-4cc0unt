<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('useradmin', function (Blueprint $table) {
            $table->increments('AdminUserId');
            $table->string('FullName');
            $table->string('Email')->nullable();
            $table->string('Password', 60)->nullable();
            $table->string('BranchId')->nullable();
            $table->string('Role')->nullable();
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
