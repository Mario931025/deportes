<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\CitySeeder;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();            
            $table->string('name');
            $table->foreignId('country_id');
            $table->timestamps();
            
            $table->foreign('country_id')
                  ->references('id')->on('countries')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->unique(['name', 'country_id']);
        });
        
        $seeder = new CitySeeder();
        $seeder->run();        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
