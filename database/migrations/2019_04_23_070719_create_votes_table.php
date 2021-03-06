<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateVotesTable.
 */
class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_vote');
            $table->string('list_films');
            $table->string('background');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->string('status_vote');
            $table->integer('room_id')->default(0);
            $table->string('detail');
            $table->dateTime('time_voting');
            $table->dateTime('time_registing');
            $table->dateTime('time_booking_chair');
            $table->dateTime('time_end');
            $table->integer('total_ticket')->default(0);
            $table->dateTime('infor_time')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::drop('votes');
    }
}
