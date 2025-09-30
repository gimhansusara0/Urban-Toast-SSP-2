<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // point to `customers` table (not `users`)
            $table->foreignId('user_id')->constrained('customers')->cascadeOnDelete();

            // keeping the simple status semantics you had
            $table->string('status')->default('paid'); // when created via checkout => paid
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
    