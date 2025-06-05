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
        Schema::create('loan_agunans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->notNull()->references('id')->on('loans')->onUpdate('cascade');
            $table->string('agunan_type');
            $table->string('doc_number');
            $table->text('doc_detail');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_agunans');
    }
};
