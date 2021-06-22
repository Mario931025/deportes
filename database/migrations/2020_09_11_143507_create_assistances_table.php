<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id');
            $table->foreignId('instructor_user_id');
            $table->double('student_latitude', 8, 5);
            $table->double('student_longitude', 8, 5); 
            $table->double('instructor_latitude', 8, 5);
            $table->double('instructor_longitude', 8, 5);
            $table->foreignId('academy_id');
            $table->boolean('is_exam')->default(true);
            $table->timestamps();
            
            $table->foreign('student_user_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('instructor_user_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->foreign('academy_id')
                  ->references('id')->on('academies')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistances');
    }
}
