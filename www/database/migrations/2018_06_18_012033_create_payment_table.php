<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_ref')->unsigned()->index();
            $table->foreign('bill_ref')->references('id')->on('bill')->onDelete('cascade');
            $table->string('method');
            $table->string('cause')->nullable();

            $table->integer( 'user_recive')->nullable()->unsigned()->index();
            $table->foreign('user_recive')->references('id')->on('users')->onDelete('cascade');

            $table->integer( 'user_void')->nullable()->unsigned()->index();
            $table->foreign('user_void')->references('id')->on('users')->onDelete('cascade');

            $table->integer( 'branch_id')->unsigned()->index();
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('cascade');

            $table->integer('activate');
            $table->double('value');
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
        Schema::dropIfExists('payment');
    }
}
