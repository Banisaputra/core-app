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
        Schema::create('instalments', function (Blueprint $table) {
            $table->id();
            $table->string('ins_code', 50)->unique();
            $table->foreignId('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->string('ins_date', 8);
            $table->string('ins_value', 100);
            $table->integer('loan_remaining');
            $table->integer('tenor');
            $table->string('status', 25);
            $table->text('remark')->nullable();
            $table->string('proof_of_payment')->comment('url payment');
            $table->string('loan_interest', 100);
            $table->integer('forfeit');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instalments');
    }
};
