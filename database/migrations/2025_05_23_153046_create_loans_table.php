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
            $table->string('loan_type', 50)->default('UANG');
            $table->integer('ref_doc_id')->nullable()->default(0);
            $table->integer('loan_date')->length(8)->comment('Ymd');
            $table->integer('loan_value');
            $table->integer('loan_tenor')->length(8);
            $table->decimal('interest_percent', 5, 2);
            $table->integer('due_date')->length(8)->comment('Ymd');
            $table->tinyInteger('loan_state')->default(1)->comment('1=pengajuan; 2=disetujui; 3=selesai; 99=dibatalkan');
            $table->integer('date_approved')->length(8)->nullable()->comment('Ymd');
            $table->tinyInteger('cut_off_record')->default(1)->comment('jika ada perubahan setting cut off');
            $table->text('remark_approval')->nullable()->comment('for rejected only');
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
