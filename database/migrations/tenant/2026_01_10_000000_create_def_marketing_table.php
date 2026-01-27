<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CAMPAIGN_TYPE = 'def_mkt_campaign_type';
    private const TABLE_CAMPAIGN_TYPE_TRANSLATION = 'def_mkt_campaign_type_translation';
    private const TABLE_GIFT_VOUCHER_THEME = 'def_mkt_gift_voucher_theme';
    private const TABLE_GIFT_VOUCHER_THEME_TRANSLATION = 'def_mkt_gift_voucher_theme_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_CAMPAIGN_TYPE, function (Blueprint $table) {
            $table->comment('Pazarlama kampanya türleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_type_id')->comment('Kampanya türü için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kampanya türü kodu');
            $table->string('name', 255)->comment('Kampanya türü adı');
            $table->string('image_url', 255)->nullable()->comment('Kampanya türü görseli');
            $table->text('settings')->nullable()->comment('Varsayılan ayarlar (JSON)');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_camp_type_soft_delete');
        });

        Schema::create(self::TABLE_CAMPAIGN_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kampanya türü çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('campaign_type_id')->comment('Kampanya türü kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Kampanya türü adı');
            $table->string('summary', 255)->nullable()->comment('Özet');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['campaign_type_id', 'language_code'], 'idx_camp_type_lang_unique');
            $table->index('deleted_at', 'idx_camp_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_GIFT_VOUCHER_THEME, function (Blueprint $table) {
            $table->comment('Hediye çeki temaları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('gift_voucher_theme_id')->comment('Tema benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz tema kodu');
            $table->string('name', 255)->comment('Tema adı');
            $table->string('image_url', 255)->nullable()->comment('Görsel URL');
            $table->text('content')->nullable()->comment('HTML/JSON İçerik');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_gv_theme_soft_delete');
        });

        Schema::create(self::TABLE_GIFT_VOUCHER_THEME_TRANSLATION, function (Blueprint $table) {
            $table->comment('Hediye çeki teması çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('gift_voucher_theme_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('gift_voucher_theme_id')->comment('Tema kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Tema adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['gift_voucher_theme_id', 'language_code'], 'idx_gv_theme_lang_unique');
            $table->index('deleted_at', 'idx_gv_theme_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_GIFT_VOUCHER_THEME_TRANSLATION);
        Schema::dropIfExists(self::TABLE_GIFT_VOUCHER_THEME);
        Schema::dropIfExists(self::TABLE_CAMPAIGN_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CAMPAIGN_TYPE);
    }
};
