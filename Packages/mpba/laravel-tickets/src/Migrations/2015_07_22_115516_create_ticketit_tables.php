<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color');
        });

        Schema::create('tickets_priorities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color');
        });

        Schema::create('tickets_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color');
        });

        Schema::create('tickets_categories_users', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->integer('user_id')->unsigned();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->longText('content');
            $table->integer('status_id')->unsigned();
            $table->integer('priority_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('agent_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('tickets_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('tickets_audits', function (Blueprint $table) {
            $table->increments('id');
            $table->text('operation');
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
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
        Schema::drop('tickets_audits');
        Schema::drop('tickets_comments');
        Schema::drop('tickets');
        Schema::drop('tickets_categories_users');
        Schema::drop('tickets_categories');
        Schema::drop('tickets_priorities');
        Schema::drop('tickets_statuses');
    }
}
