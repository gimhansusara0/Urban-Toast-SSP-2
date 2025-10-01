<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->tinyInteger('rating')->unsigned()->default(5); // 1..5
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('approved')->default(true); // demo default true
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['customer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
