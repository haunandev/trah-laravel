<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('role_code')->unique();
            $table->string('role_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            'id' => -1,
            'role_code' => 'super-admin',
            'role_name' => 'Super Admin'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
