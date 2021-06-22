<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id');
            $table->foreignId('student_user_id');
            $table->foreignId('instructor_user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('grade_id')
                  ->references('id')->on('grades')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->foreign('student_user_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('instructor_user_id')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->unique(['grade_id', 'student_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
