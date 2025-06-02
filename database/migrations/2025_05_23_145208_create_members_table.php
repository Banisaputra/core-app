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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nip', 50);
            $table->string('name', 191);
            $table->string('telphone', 20);
            $table->string('religion', 20)->nullable();
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('employment', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->date('date_joined');
            $table->tinyInteger('is_transactional')->length(2)->default(1);
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
        Schema::dropIfExists('members');
    }
};
