<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('moduleid');
            $table->timestamps();
        });

        // Insert default values
        DB::table('modules')->insert([
            ['id' => '1', 'module' => 'Academic', 'moduleid' => '1'],
            ['id' => '11', 'module' => 'Academic Misc', 'moduleid' => '11'],
            ['id' => '2', 'module' => 'Hostel', 'moduleid' => '2'],
            ['id' => '22', 'module' => 'Hostel Misc', 'moduleid' => '22'],
            ['id' => '3', 'module' => 'Transport', 'moduleid' => '3'],
            ['id' => '33', 'module' => 'Transport Misc', 'moduleid' => '33'],
        ]);        
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
