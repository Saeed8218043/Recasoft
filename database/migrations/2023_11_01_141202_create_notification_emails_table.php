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
        Schema::create('notification_emails', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('notification_id')->nullable();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->boolean('notification_type')->nullable()->default(false);
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
        Schema::dropIfExists('notification_emails');
    }
};
