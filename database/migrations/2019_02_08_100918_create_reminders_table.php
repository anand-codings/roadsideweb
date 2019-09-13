<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title');

            $table->date('purchase_date')->nullable();
            $table->time('purchase_time')->nullable();
            $table->boolean('purchase_sent')->default(0);
            
            $table->date('insurance_date')->nullable();
            $table->time('insurance_time')->nullable();
            $table->boolean('insurance_sent')->default(0);
            
            $table->date('maintainence_date')->nullable();
            $table->time('maintainence_time')->nullable();
            $table->boolean('maintainence_sent')->default(0);
            
            $table->boolean('all_sent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
}
