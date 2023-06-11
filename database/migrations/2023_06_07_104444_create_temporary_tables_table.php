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
        Schema::create('temporary_tables', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('session')->nullable();
            $table->string('alloted_category')->nullable();
            $table->string('voucher_type')->nullable();
            $table->string('voucher_no')->nullable();
            $table->string('roll_no')->nullable();
            $table->string('admno_uniqueid')->nullable();
            $table->string('status')->nullable();
            $table->string('fee_category')->nullable();
            $table->string('faculty')->nullable();
            $table->string('program')->nullable();
            $table->string('department')->nullable();
            $table->string('batch')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('fee_head')->nullable();
            $table->decimal('due_amount', 8, 2)->nullable();
            $table->decimal('paid_amount', 8, 2)->nullable();
            $table->decimal('concession_amount', 8, 2)->nullable();
            $table->decimal('scholarship_amount', 8, 2)->nullable();
            $table->decimal('reverse_concession_amount', 8, 2)->nullable();
            $table->decimal('write_off_amount', 8, 2)->nullable();
            $table->decimal('adjusted_amount', 8, 2)->nullable();
            $table->decimal('refund_amount', 8, 2)->nullable();
            $table->decimal('fund_transfer_amount', 8, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_tables');
    }
};
