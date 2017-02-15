<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('book_number')->comment('书号');
            $table->integer('type_id')->comment('图书类别');
            $table->string('name')->comment('书名');
            $table->string('press')->comment('出版社');
            $table->integer('publication_year')->comment('出版年份');
            $table->string('author')->comment('作者');
            $table->float('price')->comment('价格');
            $table->integer('cur_total')->default(0)->comment('在库量');
            $table->integer('total')->default(0)->comment('总藏量');
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
        Schema::drop('books_info');
    }
}
