<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBorrowInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrow_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('借阅者id');
            $table->integer('book_id')->comment('图书id');
            $table->timestamp('end_time')->comment('截止时间');
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
        Schema::drop('borrow_info');
    }
}
