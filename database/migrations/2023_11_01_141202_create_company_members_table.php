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
        Schema::create('company_members', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->default(0);
            $table->integer('comp_id')->default(0);
            $table->string('email')->nullable();
            $table->string('company_name')->nullable();
            $table->boolean('role')->nullable()->comment('0=>Company Owner,1=>User, 2=>Company_Admin');
            $table->string('company_id')->nullable();
            $table->integer('parent_id')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_members');
    }
};
