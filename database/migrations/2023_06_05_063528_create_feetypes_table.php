<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeetypesTable extends Migration
{
    public function up()
    {
        Schema::create('feetypes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_category');
            $table->string('f_name');
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('br_id');
            $table->string('seq_id');
            $table->string('fee_type_ledger');
            $table->unsignedBigInteger('fee_head_type');
            $table->timestamps();

            $table->foreign('fee_category')->references('id')->on('feecategory');
            $table->foreign('collection_id')->references('id')->on('feecollectiontypes');
            $table->foreign('br_id')->references('id')->on('branches');
            $table->foreign('fee_head_type')->references('id')->on('modules');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feetypes');
    }
}
