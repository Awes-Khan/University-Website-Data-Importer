<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonfeecollectionheadwiseTable extends Migration
{
    public function up()
    {
        Schema::create('commonfeecollectionheadwise', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moduleId');
            $table->unsignedBigInteger('receiptId');
            $table->unsignedBigInteger('headId');
            // $table->unsignedBigInteger('headName');
            $table->string('headName');
            $table->unsignedBigInteger('brid');
            $table->string('amount');
            $table->timestamps();

            $table->foreign('moduleId')->references('id')->on('modules');
            $table->foreign('receiptId')->references('id')->on('commonfeecollection');
            $table->foreign('headId')->references('id')->on('feetypes');
            // $table->foreign('head_name')->references('f_name')->on('feetypes');
            $table->foreign('brid')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commonfeecollectionheadwise');
    }
}


