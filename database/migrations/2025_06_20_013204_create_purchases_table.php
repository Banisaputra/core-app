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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('pr_code', 50)->unique();
            $table->integer('pr_date')->length(8);
            $table->string('ref_doc', 100);
            $table->string('supplier', 255);
            $table->integer('total');
            $table->tinyInteger('pr_state')->default(1);
            $table->string('file_path', 255)->nullable();
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('purchase_detail', function (Blueprint $table) {
            $table->foreignId('pr_id');
            $table->foreignId('item_id');
            $table->integer('amount');
            $table->integer('price');
            $table->integer('total');
            $table->string('batch', 15)->comment('YmdHis')->nullable();
            $table->integer('margin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_detail');
    }
};
