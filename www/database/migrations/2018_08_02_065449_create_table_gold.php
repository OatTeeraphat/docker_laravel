<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGold extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gold', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_ref')->unsigned()->index();
            $table->foreign('bill_ref')->references('id')->on('bill')->onDelete('cascade');

            $table->integer( 'craft_id')->nullable()->unsigned()->index();
            $table->foreign('craft_id')->references('id')->on('craft')->onDelete('cascade');

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
        //
    }
}
