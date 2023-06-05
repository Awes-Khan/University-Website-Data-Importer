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
            $table->string('head_name');
            $table->timestamps();

            $table->foreign('financialTranId')->references('id')->on('financialtrans');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financialtrandetails');
    }
}
