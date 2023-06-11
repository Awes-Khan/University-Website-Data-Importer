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
            $table->string('transId');
            $table->string('admno');
            $table->string('rollno');
            $table->unsignedBigInteger('brid');
            $table->float('amount',10,2);
            $table->string('acadamicYear');
            $table->string('financialYear');
            $table->string('displayReceiptNo')->nullable();
            $table->unsignedBigInteger('entrymode');
            $table->string('paid_date');
            $table->string('inactive')->nullable();
            $table->timestamps();

            $table->foreign('moduleId')->references('id')->on('modules');
            $table->foreign('brid')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commonfeecollection');
    }
}
