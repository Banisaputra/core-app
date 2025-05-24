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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code', 50);
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('loan_date', 8)->comment('Ymd');
            $table->string('due_date', 8)->comment('Ymd');
            $table->integer('loan_value');
            $table->integer('interest_percent');
            $table->integer('loan_tenor');
            $table->string('loan_status');
            $table->text('remark')->nullable()->comment('for rejected only');
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
        Schema::dropIfExists('loans');
    }
};
