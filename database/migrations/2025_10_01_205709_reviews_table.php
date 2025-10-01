<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Link to customers, optional (if a review is by a guest or no mapping)
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();

            // Product ID is now optional -> allows "general" reviews
            $table->unsignedBigInteger('product_id')->nullable();

            // Rating 1..5
            $table->tinyInteger('rating')->unsigned()->default(5);

            $table->string('title')->nullable();
            $table->text('body')->nullable();

            // Default approved = true (demo style)
            $table->boolean('approved')->default(true);

            $table->timestamps();

            // Indexes for query performance
            $table->index(['product_id']);
            $table->index(['customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
