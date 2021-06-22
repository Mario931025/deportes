<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\UserSeeder;

class AddColumnsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();          
            $table->foreignId('city_id')->nullable();
            $table->string('document_number')->nullable();
            $table->date('birthday')->nullable();
            $table->foreignId('academy_id')->nullable();
            $table->foreignId('role_id')->nullable();
            $table->string('profile_photo')->nullable();
            $table->foreignId('grade_id')->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('city_id')
                  ->references('id')->on('cities')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('academy_id')
                  ->references('id')->on('academies')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');                  
            
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('grade_id')
                  ->references('id')->on('grades')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');                   
        });
        
        $seeder = new UserSeeder();
        $seeder->run();          
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['academy_id']);
            $table->dropForeign(['role_id']);
            
            $table->dropColumn([
                'last_name',
                'phone',
                'city_id',
                'document_number',
                'birthday',
                'academy_id',
                'role_id',
                'profile_photo',
                'grade_id',
                'active'
            ]);
        });
    }
}
