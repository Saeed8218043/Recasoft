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
        Schema::create('request_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('company_id', 225);
            $table->string('company_name', 225);
            $table->string('phone', 225);
            $table->string('request_email', 225);
            $table->string('comments', 225);
            $table->text('devices');
            $table->string('attachment', 225);
            $table->string('urgent', 225);
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_logs');
    }
};
