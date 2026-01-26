<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_download', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('download_id');
            $table->uuid('uuid')->unique();
            $table->string('code', 100)->unique();
            $table->string('filename', 500)->comment('File path');
            $table->string('mask', 255)->nullable()->comment('Display filename');
            $table->unsignedInteger('file_size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
        });

        Schema::create('cat_download_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('download_translation_id');
            $table->unsignedBigInteger('download_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['download_id', 'language_code'], 'idx_download_lang_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_download_translation');
        Schema::dropIfExists('cat_download');
    }
};
