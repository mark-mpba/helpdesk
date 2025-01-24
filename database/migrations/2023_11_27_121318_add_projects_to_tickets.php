<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index('project_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_project_id_index');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });
    }
};
