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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sa_code', 50);
            $table->integer('sa_date')->length(8);
            $table->integer('member_id')->references('id')->on('members');
            $table->integer('sub_total');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->foreignId('sa_id');
            $table->foreignId('item_id');
            $table->integer('amount');
            $table->integer('price');
            $table->integer('total');
            $table->timestamps();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('pr_code', 50);
            $table->integer('pr_date')->length(8);
            $table->string('supplier', 100);
            $table->integer('sub_total');
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
            $table->string('batch', 15)->comment('YmdHis');
            $table->integer('margin');

            $table->timestamps();
        });

        // Schema::create('inventories', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('iv_code', 50);
        //     $table->integer('iv_date')->length(8);
        //     $table->string('source_code', 50);
        //     $table->integer('amount');
        //     $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
        //     $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
        //     $table->timestamps();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sales_detail');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_detail');
        // Schema::dropIfExists('inventories');
    }
};
