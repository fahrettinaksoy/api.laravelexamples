<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ctlg_review', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('review_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('account_id')->default(0)->index('idx_auto')->comment('Customer ID');
            $table->unsignedBigInteger('order_id')->default(0)->comment('Verified purchase order ID');
            $table->string('code', 100)->unique();
            $table->string('author', 255);
            $table->string('email', 255)->nullable();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5)->index('idx_auto')->comment('1-5 stars');
            $table->unsignedTinyInteger('quality_rating')->default(0)->comment('Quality rating');
            $table->unsignedTinyInteger('value_rating')->default(0)->comment('Value for money');
            $table->unsignedTinyInteger('service_rating')->default(0)->comment('Service rating');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_verified_purchase')->default(false)->index();
            $table->boolean('is_active')->default(false)->index('idx_auto')->comment('Approved status');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'is_active', 'rating']);
            $table->index(['account_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctlg_review');
    }
};
