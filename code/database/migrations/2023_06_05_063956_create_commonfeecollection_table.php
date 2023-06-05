<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonfeecollectionTable extends Migration
{
    public function up()
    {
        Schema::create('commonfeecollection', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moduleId');
            $table->unsignedBigInteger('receiptId');
            $table->unsignedBigInteger('headId');
            $table->string('headName');
            $table->unsignedBigInteger('brid');
            $table->string('amount');
            $table->timestamps();

            $table->foreign('moduleId')->references('id')->on('modules');
            $table->foreign('headId')->references('id')->on('feetypes');
            $table->foreign('brid')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commonfeecollection');
    }
}
