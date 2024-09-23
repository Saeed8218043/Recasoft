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
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_active')->default(true);
            $table->integer('signal_strength')->default(0);
            $table->integer('battery_level')->default(0);
            $table->string('company_id', 20);
            $table->string('temperature');
            $table->string('device_id', 20);
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('device_status');
            $table->string('event_type', 100)->nullable();
            $table->integer('user_id');
            $table->string('sensor_id', 225)->default('0');
            $table->string('transmissionMode', 25)->nullable();
            $table->integer('coming_from_id')->default(0);
            $table->timestamps();
            $table->timestamp('battery_updated_datetime')->nullable();
            $table->timestamp('temeprature_last_updated')->nullable();
            $table->string('macAddress')->nullable();
            $table->string('ipAddress')->nullable();
            $table->integer('sort')->nullable();
            $table->text('labels_json')->nullable();
            $table->tinyInteger('not_available')->default(0);
            $table->string('specification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
