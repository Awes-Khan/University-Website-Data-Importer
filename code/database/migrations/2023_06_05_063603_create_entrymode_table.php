<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrymodeTable extends Migration
{
    public function up()
    {
        Schema::create('entrymode', function (Blueprint $table) {
            $table->id();
            $table->string('entry_modename');
            $table->string('crdr');
            $table->integer('entrymodeno');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrymode');
    }
}
