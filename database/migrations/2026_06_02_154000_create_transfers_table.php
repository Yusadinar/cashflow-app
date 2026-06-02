<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfers')) {
            Schema::create('transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('from_payment_method_id')->constrained('payment_methods')->restrictOnDelete();
                $table->foreignId('to_payment_method_id')->constrained('payment_methods')->restrictOnDelete();
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->date('transfer_date');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
