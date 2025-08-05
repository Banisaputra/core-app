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
        Schema::create('saving_types', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('value')->nullable();
            $table->unsignedTinyInteger('auto_day')->nullable();
            $table->tinyInteger('is_auto')->default(0);
            $table->tinyInteger('is_transactional')->default(1);
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
        Schema::dropIfExists('saving_types');
    }
};
