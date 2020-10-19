<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('export', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->string('file_name');
            $table->string('size');
            $table->string('no_record');
            $table->string('update_at');

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
