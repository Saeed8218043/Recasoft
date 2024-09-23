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
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('company_id', 50)->nullable();
            $table->tinyInteger('isActive')->nullable()->default(1);
            $table->tinyInteger('isResolved')->default(0);
            $table->string('name')->nullable();
            $table->string('alert_type')->nullable();
            $table->string('temp_range', 50)->nullable();
            $table->string('upper_celcius', 50)->nullable();
            $table->string('lower_celcius', 50)->nullable();
            $table->integer('maintenance_repeat')->nullable();
            $table->timestamp('m_date')->nullable();
            $table->integer('delay_time')->nullable();
            $table->timestamps();
            $table->integer('reminder_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
