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

        // Inserting values
        DB::table('entrymode')->insert([
        ["entry_modename"=>"DUE","crdr"=>"D","entrymodeno"=>0],
        ["entry_modename"=>"REVDUE","crdr"=>"C","entrymodeno"=>12],
        ["entry_modename"=>"SCHOLARSHIP","crdr"=>"C","entrymodeno"=>15],
        ["entry_modename"=>"REVSCHOLARSHIP","crdr"=>"D","entrymodeno"=>16],
        ["entry_modename"=>"REVCONCESSION","crdr"=>"D","entrymodeno"=>16],
        ["entry_modename"=>"CONCESSION","crdr"=>"C","entrymodeno"=>15],
        ["entry_modename"=>"RCPT","crdr"=>"C","entrymodeno"=>0],
        ["entry_modename"=>"REVRCPT","crdr"=>"D","entrymodeno"=>0],
        ["entry_modename"=>"JV","crdr"=>"C","entrymodeno"=>14],
        ["entry_modename"=>"REVJV","crdr"=>"D","entrymodeno"=>14],
        ["entry_modename"=>"PMT","crdr"=>"D","entrymodeno"=>1],
        ["entry_modename"=>"REVPMT","crdr"=>"C","entrymodeno"=>1],
        ["entry_modename"=>"Fundtransfer","crdr"=>"positive and negative","entrymodeno"=>1]
        ]);
    }    
    public function down()
    {
        Schema::dropIfExists('entrymode');
    }
}
