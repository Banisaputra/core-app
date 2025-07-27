<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->integer('inv_date')->length(8);
            $table->string('type', 100);
            $table->text('remark');
            $table->tinyInteger('inv_state')->default(1);
            $table->foreignId('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
       
        Schema::create('inventory_detail', function (Blueprint $table) {
            $table->foreignId('inv_id')->references('id')->on('inventories')->onUpdate('cascade');
            $table->foreignId('item_id')->references('id')->on('master_items')->onUpdate('cascade');
            $table->integer('amount', 100);
            $table->string('batch', 15)->comment('YmdHis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('inventory_detail');
    }
};
