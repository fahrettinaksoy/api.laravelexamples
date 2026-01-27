<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CAMPAIGN = 'mkt_campaign';
    private const TABLE_CAMPAIGN_HISTORY = 'mkt_campaign_history';
    private const TABLE_CAMPAIGN_TRANSLATION = 'mkt_campaign_translation';
    private const TABLE_CAMPAIGN_PRODUCT = 'mkt_campaign_product';
    
    private const TABLE_COUPON = 'mkt_coupon';
    private const TABLE_COUPON_HISTORY = 'mkt_coupon_history';
    private const TABLE_COUPON_PRODUCT = 'mkt_coupon_product';
    private const TABLE_COUPON_CATEGORY = 'mkt_coupon_category';
    private const TABLE_COUPON_BRAND = 'mkt_coupon_brand';
    private const TABLE_COUPON_ACCOUNT_GROUP = 'mkt_coupon_account_group';
    
    private const TABLE_GIFT_VOUCHER = 'mkt_gift_voucher';

    public function up(): void
    {
        Schema::create(self::TABLE_CAMPAIGN, function (Blueprint $table) {
            $table->comment('Pazarlama kampanyalarının tanımlandığı ana tablo');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_id')->comment('Kampanya benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Kampanya kodu');
            $table->string('name', 255)->comment('Kampanya adı');
            $table->string('image_url', 255)->nullable()->comment('Kampanya görseli');
            $table->unsignedBigInteger('campaign_type_id')->default(0)->index()->comment('Kampanya türü kimliği');
            $table->text('campaign_type_setting')->nullable()->comment('Kampanya türü ayarları (JSON)');
            $table->timestamp('start_date')->nullable()->comment('Başlangıç tarihi');
            $table->timestamp('end_date')->nullable()->comment('Bitiş tarihi');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(false)->index()->comment('Durum (false: Pasif, true: Aktif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['start_date', 'end_date', 'status'], 'idx_campaign_active_period');
            $table->index(['created_by', 'created_at'], 'idx_campaign_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_campaign_updated_audit');
            $table->index('deleted_at', 'idx_campaign_soft_delete');
        });

        Schema::create(self::TABLE_CAMPAIGN_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kampanya çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('campaign_id')->comment('Kampanya kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->index()->comment('Kampanya adı (Çeviri)');
            $table->string('summary', 500)->nullable()->comment('Özet açıklama');
            $table->longText('description')->nullable()->comment('Detaylı açıklama');
            $table->longText('condition_text')->nullable()->comment('Kampanya koşulları metni');
            $table->string('keyword', 255)->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['campaign_id', 'language_code'], 'idx_campaign_lang_unique');
            $table->index('deleted_at', 'idx_campaign_trans_soft_delete');
            $table->fullText(['name', 'description'], 'idx_campaign_fulltext');
        });

        Schema::create(self::TABLE_CAMPAIGN_HISTORY, function (Blueprint $table) {
            $table->comment('Kampanya kullanım geçmişi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_history_id')->comment('Geçmiş kaydı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('campaign_id')->comment('Kampanya kimliği');
            $table->unsignedBigInteger('order_id')->comment('Sipariş kimliği');
            $table->unsignedBigInteger('account_id')->comment('Müşteri/Hesap kimliği');
            $table->string('code', 50)->nullable()->comment('Kullanılan kampanya kodu');
            $table->decimal('amount', 19, 4)->comment('İndirim tutarı');
            $table->char('currency_code', 3)->default('USD')->comment('Para birimi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['campaign_id', 'account_id'], 'idx_campaign_usage');
            $table->index(['order_id'], 'idx_campaign_order');
            $table->index('deleted_at', 'idx_campaign_hist_soft_delete');
        });

        Schema::create(self::TABLE_CAMPAIGN_PRODUCT, function (Blueprint $table) {
            $table->comment('Kampanya-Ürün ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('campaign_product_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('campaign_id')->comment('Kampanya kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['campaign_id', 'product_id'], 'idx_campaign_product_unique');
            $table->index('deleted_at', 'idx_campaign_prod_soft_delete');
        });

        Schema::create(self::TABLE_COUPON, function (Blueprint $table) {
            $table->comment('İndirim kuponları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_id')->comment('Kupon benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Kupon kodu');
            $table->string('name', 255)->comment('Kupon adı');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage')->comment('İndirim türü: Yüzde veya Sabit');
            $table->decimal('discount', 19, 4)->comment('İndirim değeri');
            $table->decimal('min_total', 19, 4)->default(0)->comment('Minimum sepet tutarı');
            $table->boolean('shipping')->default(false)->comment('Ücretsiz kargo mu?');
            $table->date('date_start')->nullable()->comment('Başlangıç tarihi');
            $table->date('date_end')->nullable()->comment('Bitiş tarihi');
            $table->unsignedInteger('uses_total')->default(0)->comment('Toplam kullanım limiti');
            $table->unsignedInteger('uses_customer')->default(0)->comment('Müşteri başı kullanım limiti');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['date_start', 'date_end', 'status'], 'idx_coupon_validity');
            $table->index('deleted_at', 'idx_coupon_soft_delete');
        });

        Schema::create(self::TABLE_COUPON_HISTORY, function (Blueprint $table) {
            $table->comment('Kupon kullanım geçmişi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_history_id')->comment('Geçmiş kaydı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('coupon_id')->comment('Kupon kimliği');
            $table->unsignedBigInteger('order_id')->comment('Sipariş kimliği');
            $table->unsignedBigInteger('account_id')->comment('Müşteri/Hesap kimliği');
            $table->decimal('amount', 19, 4)->comment('İndirim tutarı');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['coupon_id', 'account_id'], 'idx_coupon_usage_history');
            $table->index('deleted_at', 'idx_coupon_hist_soft_delete');
        });

        Schema::create(self::TABLE_COUPON_PRODUCT, function (Blueprint $table) {
            $table->comment('Kupon-Ürün kısıtlama tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_product_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('coupon_id')->comment('Kupon kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['coupon_id', 'product_id'], 'idx_coupon_product_unique');
            $table->index('deleted_at', 'idx_coupon_prod_soft_delete');
        });

        Schema::create(self::TABLE_COUPON_CATEGORY, function (Blueprint $table) {
            $table->comment('Kupon-Kategori kısıtlama tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_category_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('coupon_id')->comment('Kupon kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['coupon_id', 'category_id'], 'idx_coupon_category_unique');
            $table->index('deleted_at', 'idx_coupon_cat_soft_delete');
        });

        Schema::create(self::TABLE_COUPON_BRAND, function (Blueprint $table) {
            $table->comment('Kupon-Marka kısıtlama tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_brand_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('coupon_id')->comment('Kupon kimliği');
            $table->unsignedBigInteger('brand_id')->comment('Marka kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['coupon_id', 'brand_id'], 'idx_coupon_brand_unique');
            $table->index('deleted_at', 'idx_coupon_brand_soft_delete');
        });

        Schema::create(self::TABLE_COUPON_ACCOUNT_GROUP, function (Blueprint $table) {
            $table->comment('Kupon-Müşteri Grubu kısıtlama tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('coupon_account_group_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('coupon_id')->comment('Kupon kimliği');
            $table->unsignedBigInteger('account_group_id')->comment('Müşteri grubu kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['coupon_id', 'account_group_id'], 'idx_coupon_acc_grp_unique');
            $table->index('deleted_at', 'idx_coupon_acc_grp_soft_delete');
        });

        Schema::create(self::TABLE_GIFT_VOUCHER, function (Blueprint $table) {
            $table->comment('Hediye çeki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('gift_voucher_id')->comment('Hediye çeki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('order_id')->default(0)->comment('Satın alma siparişi kimliği');
            $table->unsignedBigInteger('gift_voucher_theme_id')->comment('Tema kimliği');
            $table->string('code', 20)->unique()->comment('Hediye çeki kodu');
            $table->string('from_name', 255)->comment('Gönderen adı');
            $table->string('from_email', 255)->comment('Gönderen e-posta');
            $table->string('to_name', 255)->comment('Alıcı adı');
            $table->string('to_email', 255)->comment('Alıcı e-posta');
            $table->text('message')->nullable()->comment('Mesaj');
            $table->decimal('amount', 19, 4)->comment('Tutar');
            $table->boolean('status')->default(true)->comment('Durum (Aktif/Pasif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['code', 'status'], 'idx_gv_code_status');
            $table->index('deleted_at', 'idx_gv_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_GIFT_VOUCHER);
        Schema::dropIfExists(self::TABLE_COUPON_ACCOUNT_GROUP);
        Schema::dropIfExists(self::TABLE_COUPON_BRAND);
        Schema::dropIfExists(self::TABLE_COUPON_CATEGORY);
        Schema::dropIfExists(self::TABLE_COUPON_PRODUCT);
        Schema::dropIfExists(self::TABLE_COUPON_HISTORY);
        Schema::dropIfExists(self::TABLE_COUPON);
        Schema::dropIfExists(self::TABLE_CAMPAIGN_PRODUCT);
        Schema::dropIfExists(self::TABLE_CAMPAIGN_HISTORY);
        Schema::dropIfExists(self::TABLE_CAMPAIGN_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CAMPAIGN);
    }
};
