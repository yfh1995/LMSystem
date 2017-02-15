<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30)->comment('名字');
            $table->integer('identity')->nullable()->comment('身份，0：学生，1：老师');
            $table->string('major')->nullable()->comment('专业');
            $table->string('grade')->nullable()->comment('年级');
            $table->string('id_number')->comment('证件号码');
            $table->boolean('sex')->comment('性别，true：男，false：女');
            $table->integer('available_num')->comment('可借阅数量');
            $table->integer('sum_num')->comment('总借阅数量');
            $table->string('phone',20)->comment('手机号');
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
        Schema::drop('user_info');
    }
}
