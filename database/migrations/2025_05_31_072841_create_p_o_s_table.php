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
            $table->string('payment_type')->default('KREDIT');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::create('sale_detail', function (Blueprint $table) {
            $table->foreignId('sa_id');
            $table->foreignId('item_id');
            $table->integer('amount');
            $table->integer('price');
            $table->integer('disc_price')->default(0);
            $table->integer('total');
            $table->timestamps();
        }); 

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_detail');
        Schema::dropIfExists('sales');
    }
};
