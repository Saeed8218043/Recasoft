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
        Schema::create('documents', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 225);
            $table->string('company_id', 225);
            $table->integer('parent_id')->default(0);
            $table->integer('type')->default(0);
            $table->integer('belongsTo')->default(0);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
            $table->string('slug', 225)->nullable()->unique('slug');
            $table->string('file', 225)->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
