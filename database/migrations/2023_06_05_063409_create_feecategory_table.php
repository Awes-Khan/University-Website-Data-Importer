<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeecategoryTable extends Migration
{
    public function up()
    {
        Schema::create('feecategory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('brid');
            $table->timestamps();
            $table->foreign('brid')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feecategory');
    }
}
