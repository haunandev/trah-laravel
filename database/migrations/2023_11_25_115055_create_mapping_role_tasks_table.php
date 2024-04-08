<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingRoleTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_role_tasks', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(0);
            $table->bigInteger('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->bigInteger('task_id')->references('id')->on('tasks')->onDelete('cascade');
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
        Schema::dropIfExists('mapping_role_tasks');
    }
}
