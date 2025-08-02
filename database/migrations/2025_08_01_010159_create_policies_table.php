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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('doc_type', 100);
            $table->text('description');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('policy_detail', function (Blueprint $table) {
            $table->foreignId('pl_id')->notNull()->references('id')->on('policies')->onUpdate('cascade');
            $table->integer('min_year')->nullable();
            $table->integer('max_year')->nullable();
            $table->integer('min_value')->nullable();
            $table->integer('max_value')->nullable();
            $table->text('file_path')->nullable();
            $table->tinyInteger('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
