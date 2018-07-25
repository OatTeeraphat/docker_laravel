<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date');
            $table->date('date_');
            $table->integer( 'activate');

            $table->integer('bill_ref')->unsigned()->index();
            $table->foreign('bill_ref')->references('id')->on('bill')->onDelete('cascade');

            $table->integer('customer_id')->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');

            $table->integer('branch_id')->unsigned()->index();
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('job_id')->unsigned()->index();
            $table->foreign('job_id')->references('id')->on('job')->onDelete('cascade');

            $table->integer('amulet_id')->unsigned()->index();
            $table->foreign('amulet_id')->references('id')->on('amulet')->onDelete('cascade');

            $table->integer('amount');
            $table->double('price');
            $table->string('desc')->nullable();
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
        Schema::dropIfExists('order');
    }
}
