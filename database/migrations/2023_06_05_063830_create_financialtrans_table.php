<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialtransTable extends Migration
{
    public function up()
    {
        Schema::create('financialtrans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tranid')->unique();
            $table->unsignedBigInteger('moduleid');
            // $table->string('transid');
            $table->string('admno');
            $table->float('amount',10,2);
            $table->string('crdr');
            $table->string('tranDate');
            $table->string('acadYear');
            $table->unsignedBigInteger('entrymode');
            $table->integer('voucherno');
            $table->unsignedBigInteger('brid');
            $table->integer('Type_of_concession')->nullable();
            $table->timestamps();

            $table->foreign('moduleid')->references('id')->on('modules');
            $table->foreign('brid')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financialtrans');
    }
}
