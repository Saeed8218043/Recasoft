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
        Schema::create('notification_devices', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('device_id')->nullable();
            $table->dateTime('last_deviate_time')->nullable();
            $table->bigInteger('notification_id')->nullable();
            $table->boolean('already_sent')->default(false);
            $table->tinyInteger('resolve_sent')->default(0);
            $table->integer('maintenance_sent')->default(0);
            $table->timestamps();
            $table->boolean('reminder_sent')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_devices');
    }
};
