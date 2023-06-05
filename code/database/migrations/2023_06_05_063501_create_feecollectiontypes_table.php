<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeecollectiontypesTable extends Migration
{
    public function up()
    {
        Schema::create('feecollectiontypes', function (Blueprint $table) {
            $table->id();
            $table->string('collectionhead');
            $table->string('collectiondesc');
            $table->unsignedBigInteger('br_id');
            $table->timestamps();

            $table->foreign('br_id')->references('id')->on('branches');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feecollectiontypes');
    }
}
