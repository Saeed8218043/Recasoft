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
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_id', 20);
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->string('service_account_id')->nullable();
            $table->string('service_account_email')->nullable();
            $table->string('key_id')->nullable();
            $table->tinyInteger('is_active');
            $table->integer('parent_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('organization_name')->nullable();
            $table->string('organization_no')->nullable();
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
        Schema::dropIfExists('companies');
    }
};
