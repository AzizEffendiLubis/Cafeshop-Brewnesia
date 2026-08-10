<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('table_id')->constrained()->onDelete('restrict');
            $table->string('customer_name')->default('Tamu');
            $table->string('session_id')->nullable();
            $table->decimal('subtotal', 12, 0)->default(0);
            $table->decimal('tax', 12, 0)->default(0);
            $table->decimal('total', 12, 0)->default(0);
            $table->enum('payment_method', ['cash', 'qr'])->default('cash');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('status', ['pending', 'process', 'ready', 'done', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
