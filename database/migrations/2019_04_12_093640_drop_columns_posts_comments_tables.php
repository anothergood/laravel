<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsPostsCommentsTables extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('body');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('body');
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
