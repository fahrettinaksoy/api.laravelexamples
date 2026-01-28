<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';
	
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CHANNEL_TYPE = 'def_mktp_channel_type';
    private const TABLE_CHANNEL_TYPE_TRANSLATION = 'def_mktp_channel_type_translation';
    private const TABLE_CHANNEL_SERVICE = 'def_mktp_channel_service';
    private const TABLE_CHANNEL_SERVICE_TRANSLATION = 'def_mktp_channel_service_translation';
    private const TABLE_MARKETPLACE_CATEGORY = 'def_mktp_category';

    public function up(): void
    {
        Schema::create(self::TABLE_CHANNEL_TYPE, function (Blueprint $table) {
            $table->comment('Pazaryeri kanal tipleri (Marketplace, Sosyal Medya, XML vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('channel_type_id')->comment('Tip benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Sınıf/Kod adı (class)');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_ch_type_soft_delete');
        });

        Schema::create(self::TABLE_CHANNEL_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kanal tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('channel_type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('channel_type_id')->comment('Tip kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Tip adı');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['channel_type_id', 'language_code'], 'idx_ch_type_lang_unique');
            $table->index('deleted_at', 'idx_ch_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_CHANNEL_SERVICE, function (Blueprint $table) {
            $table->comment('Pazaryeri kanal servisleri (Ürün aktarımı, Sipariş çekme vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('channel_service_id')->comment('Servis benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Servis kodu');
            $table->text('element')->nullable()->comment('Yapılandırma elementleri (JSON/HTML)');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_ch_service_soft_delete');
        });

        Schema::create(self::TABLE_CHANNEL_SERVICE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kanal servisi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('channel_service_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('channel_service_id')->comment('Servis kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Servis adı');
            $table->string('summary', 255)->nullable()->comment('Kısa açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['channel_service_id', 'language_code'], 'idx_ch_service_lang_unique');
            $table->index('deleted_at', 'idx_ch_service_trans_soft_delete');
        });

        Schema::create(self::TABLE_MARKETPLACE_CATEGORY, function (Blueprint $table) {
            $table->comment('Pazaryeri kategori eşleşmeleri ve havuzu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('marketplace_category_id')->comment('Kayıt benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('channel_id')->default(0)->index()->comment('Kanal/Entegrasyon kimliği');
            $table->string('channel_code', 50)->nullable()->comment('Kanal kodu (n11, trendyol vb.)');
            $table->string('category_id', 255)->comment('Pazaryerisindeki kategori ID');
            $table->string('category_code', 255)->nullable()->comment('Pazaryerisindeki kategori kodu');
            $table->string('category_parent_id', 255)->nullable()->comment('Pazaryerisindeki üst kategori ID');
            $table->string('category_parent_code', 255)->nullable()->comment('Pazaryerisindeki üst kategori kodu');
            $table->string('category_name', 500)->comment('Kategori adı');
            $table->text('category_path')->nullable()->comment('Kategori yolu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['channel_id', 'category_id'], 'idx_mkt_cat_lookup');
            $table->index('deleted_at', 'idx_mkt_cat_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_MARKETPLACE_CATEGORY);
        Schema::dropIfExists(self::TABLE_CHANNEL_SERVICE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CHANNEL_SERVICE);
        Schema::dropIfExists(self::TABLE_CHANNEL_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CHANNEL_TYPE);
    }
};
