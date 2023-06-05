<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialtrandetailsTable extends Migration
{
    public function up()
    {
        Schema::create('financialtrandetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financialTranId');
            $table->unsignedBigInteger('moduleId');
            $table->string('amount');
            $table->unsignedBigInteger('headId');
            $table->string('crdr');
            $table->unsignedBigInteger('brid');
            // $table->unsignedBigInteger('head_name');
            $table->string('head_name');
            $table->timestamps();

            $table->foreign('financialTranId')->references('id')->on('financialtrans');
            $table->foreign('moduleId')->references('id')->on('modules');
            $table->foreign('headId')->references('id')->on('feetypes');
            $table->foreign('brid')->references('id')->on('branches');
            // $table->foreign('head_name')->references('f_name')->on('feetypes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financialtrandetails');
    }
}
