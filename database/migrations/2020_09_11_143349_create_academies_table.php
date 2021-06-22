<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\AcademySeeder;

class CreateAcademiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academies', function (Blueprint $table) {
            $table->id();            
            $table->string('name');
            $table->foreignId('country_id');
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('country_id')
                  ->references('id')->on('countries')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->unique(['name', 'country_id']);
        });
        
        $seeder = new AcademySeeder();
        $seeder->run();      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academies');
    }
}
