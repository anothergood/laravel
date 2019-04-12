<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_user', function (Blueprint $table) {
          $table->unsignedInteger('user_initiator_id');
          $table->unsignedInteger('user_id');
          $table->string('status',50)->index();
          $table->timestamps();

          $table->foreign('user_initiator_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_user');
    }
}
