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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string('sv_code', 50);
            $table->integer('sv_date')->length(8);
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreignId('sv_type_id')->references('id')->on('saving_types')->onDelete('cascade');
            $table->string('sv_value', 100);
            $table->string('proof_of_payment', 255)->comment('url bukti bayar');
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
        Schema::dropIfExists('savings');
    }
};
