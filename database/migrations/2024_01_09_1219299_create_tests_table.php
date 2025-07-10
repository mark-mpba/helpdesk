<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use mpba\Tickets\Models\Test;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Test::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->integer('step')->default(0);
            $table->integer('version')->default(1);
            $table->string('name');
            $table->text('details')->nullable();
            $table->text('outcome')->nullable();;
            $table->text('actual')->nullable();;
            $table->integer('passed')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Test::TABLE_NAME);
    }
};
