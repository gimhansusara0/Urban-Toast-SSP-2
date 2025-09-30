<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // point to customers
            $table->foreignId('user_id')->constrained('customers')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // order_id may be null while item is in cart
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price_each', 10, 2);

            // pending = in cart, purchased = checked out
            $table->string('status')->default('pending');
            $table->timestamp('purchased_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_items');
    }
};
