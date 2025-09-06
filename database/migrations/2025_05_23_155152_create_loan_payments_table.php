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
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->string('lp_code', 50)->unique();
            $table->foreignId('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->integer('lp_date')->length(8);
            $table->integer('lp_value');
            $table->integer('loan_interest')->comment('bunga dari angsuran');
            $table->integer('loan_remaining');
            $table->integer('lp_total');
            $table->integer('tenor_month')->length(8);
            $table->tinyInteger('lp_state')->length(2);
            $table->integer('lp_approved')->length(8)->nullable()->comment('untuk konfirmasi pelunasan');
            $table->text('remark')->nullable();
            $table->string('proof_of_payment')->nullable()->comment('url payment');
            $table->integer('lp_forfeit')->comment('denda keterlambatan');
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
        Schema::dropIfExists('loan_payments');
    }
};
