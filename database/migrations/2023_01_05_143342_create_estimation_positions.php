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
        Schema::create('estimation_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_estimation_id')->constrained('cost_estimations')
                ->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('component')->nullable();
            $table->text('comment')->nullable();
            $table->decimal('minimum_estimate',18)->nullable();
            $table->decimal('maximum_estimate',18)->nullable();
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
        Schema::dropIfExists('estimation_positions');
    }
};
