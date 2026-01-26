<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ctlg_warehouse', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('warehouse_id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('category_id')->default(0)->comment('Warehouse type/category');
            $table->string('code', 100)->unique();
            $table->string('name', 255);
            $table->string('authorized_person', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->char('country_code', 2)->nullable()->comment('ISO 3166-1 alpha-2');
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['is_active', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctlg_warehouse');
    }
};
