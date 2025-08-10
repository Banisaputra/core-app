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
        Schema::create('agunan_policies', function (Blueprint $table) {
            $table->id();
            $table->string('agp_name', 100);
            $table->string('doc_type', 100);
            $table->text('description');
            $table->string('agp_value', 100);
            $table->integer('start_year')->length(4);
            $table->integer('end_year')->length(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agunan_policies');
    }
};
