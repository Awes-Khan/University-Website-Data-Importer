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
            $table->string('tranid')->unique();
            $table->unsignedBigInteger('moduleid');
            $table->string('transid');
            $table->string('admno');
            $table->string('amount');
            $table->string('crdr');
            $table->unsignedBigInteger('brid');
            $table->string('tranDate');
            $table->string('acadYear');
            $table->unsignedBigInteger('entrymode');
            $table->string('voucherno');
            $table->string('Type_of_concession')->nullable();
            $table->timestamps();

            $table->foreign('moduleid')->references('id')->on('modules');
            $table->foreign('brid')->references('id')->on('branches');
            $table->foreign('entrymode')->references('id')->on('entrymode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financialtrans');
    }
}
