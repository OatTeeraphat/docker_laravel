<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'date');
            $table->date('date_');
            $table->integer( 'activate');
            $table->integer( 'process');
            $table->integer( 'deliver');
            $table->integer( 'pay');
            $table->integer( 'status');
            $table->integer( 'allow_zero');
            $table->string( 'job_type');
            $table->string('bill_id');
            $table->string('image_part')->nullable();

            $table->integer( 'user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer( 'customer_id')->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');

            $table->integer( 'branch_id')->unsigned()->index();
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('cascade');

            $table->integer( 'cause_id')->nullable()->unsigned()->index();
            $table->foreign('cause_id')->references('id')->on('cause')->onDelete('cascade');

            $table->integer( 'gold')->nullable();

            $table->integer( 'craft_id')->nullable()->unsigned()->index();
            $table->foreign('craft_id')->references('id')->on('craft')->onDelete('cascade');

            $table->double('cash');
            $table->string( 'desc')->nullable();

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
        Schema::dropIfExists('bill');
    }
}
