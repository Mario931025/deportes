<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAcademiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('academies', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable();

            $table->foreign('city_id')
                  ->references('id')->on('cities')
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
        Schema::table('academies', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            
            $table->dropColumn([
                'city_id',
            ]);
        });
    }
}
