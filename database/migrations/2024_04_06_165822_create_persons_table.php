<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('no_induk');
            $table->integer('no_urut')->default(0);
            $table->string('marriage', 1)->nullable();
            $table->foreignId('couple_id')->nullable();
            $table->foreignId('father_id')->nullable();
            $table->foreignId('mother_id')->nullable();
            $table->string('fullname');
            $table->string('nik', 16)->nullable();
            $table->string('no_kk', 16)->nullable();
            $table->string('username')->nullable();
            $table->string('bin')->nullable();
            $table->string('garis_trah', 1)->nullable();
            $table->text('address')->nullable();
            $table->string('gender', 1)->default('U');
            $table->string('kk_utama', 1)->nullable();
            $table->tinyInteger('died')->default(0);
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('hadir')->nullable();
            $table->text('notes')->nullable();
            $table->string('kota_desa')->nullable();
            $table->string('photo')->nullable();
            $table->date('date_of_death')->nullable();
            $table->string('place_of_death')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
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
        Schema::dropIfExists('persons');
    }
}
