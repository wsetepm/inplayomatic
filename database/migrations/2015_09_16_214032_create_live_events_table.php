<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_events', function (Blueprint $table) {
            $table->integer('match_rball_id');
            $table->integer('match_gsm_id');
            $table->integer('match_goallive_id');
            $table->string('game_half');
            $table->string('game_time');
            $table->integer('score_home');
            $table->integer('score_away');
            $table->integer('event_number');
            $table->integer('event_id');
            $table->string('event_name');
            $table->string('state');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->integer('duration');
            $table->string('status');

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
        Schema::drop('live_events');
    }
}
