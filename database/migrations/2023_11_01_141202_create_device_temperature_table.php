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
        Schema::create('device_temperature', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_id', 20);
            $table->string('device_id', 20);
            $table->double('temperature', 5, 2)->nullable();
            $table->string('signal_strength')->nullable()->default('0');
            $table->string('type', 100)->nullable();
            $table->string('cloudConnector', 20)->nullable();
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
        Schema::dropIfExists('device_temperature');
    }
};
