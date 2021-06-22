<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->timestamp('trial_expiration_date')->nullable();
            $table->timestamp('cancellation_date', 3)->nullable();
            $table->timestamp('expiration_date', 3)->nullable();
            $table->boolean('disabled')->default(false);
            $table->string('plataform')->nullable();
            $table->string('receipt')->nullable();
            $table->text('raw_data')->nullable();
            $table->string('order_id')->nullable();
            $table->timestamp('purchase_date', 3)->nullable();            
            $table->string('purchase_token')->nullable();
            $table->string('subscription_id')->nullable();   
            $table->string('package_name')->nullable();
            $table->timestamp('updation_date', 3)->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('id')->on('users')
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
        Schema::dropIfExists('subscriptions');
    }
}
