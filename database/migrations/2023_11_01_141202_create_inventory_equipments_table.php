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
        Schema::create('inventory_equipments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 225);
            $table->string('company_id', 225);
            $table->string('event_type', 225);
            $table->string('equipment_id', 225);
            $table->integer('user_id');
            $table->string('description', 225);
            $table->string('specification', 225);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_equipments');
    }
};
