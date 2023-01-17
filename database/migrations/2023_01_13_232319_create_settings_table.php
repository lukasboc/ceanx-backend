<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting');
            $table->boolean('boolean_value')->nullable()->default(true);
            $table->text('text_value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert(
            array(
                'setting' => 'allow_registrations',
                'boolean_value' => true,
                'text_value' => null,
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
