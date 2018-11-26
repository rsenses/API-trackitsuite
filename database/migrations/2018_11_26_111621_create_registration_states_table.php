<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_states', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('registration_id')->unsigned();
            $table->string('transition');
            $table->string('to');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('registration_id')
                ->references('registration_id')
                ->on('registration')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_states');
    }
}
