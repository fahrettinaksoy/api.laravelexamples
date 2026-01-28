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

    private const TABLE_APPLICATION = 'app_application';
    private const TABLE_APPLICATION_TRANSLATION = 'app_application_translation';
    private const TABLE_APPLICATION_IMAGE = 'app_application_image';
    private const TABLE_APPLICATION_FAQ = 'app_application_faq';
    private const TABLE_APPLICATION_POST = 'app_application_post';
    private const TABLE_APPLICATION_RELATED = 'app_application_related';
    private const TABLE_APPLICATION_CAMPAIGN = 'app_application_campaign';
    private const TABLE_APPLICATION_CHANNEL_SERVICES = 'app_application_channel_services';

    public function up(): void
    {
        Schema::create(self::TABLE_APPLICATION, function (Blueprint $table) {
            $table->comment('Sistem üzerindeki uygulamalar/eklentiler ve modüller');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_id')->comment('Uygulama benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->default(0)->index()->comment('Kategori kimliği');
            $table->unsignedBigInteger('developer_id')->default(0)->index()->comment('Geliştirici kimliği');
            $table->string('type', 100)->index()->comment('Uygulama tipi (Module, Theme, Channel, Payment vb.)');
            $table->string('code', 100)->unique()->comment('Uygulama benzersiz kodu');
            $table->string('icon', 500)->nullable()->comment('İkon görsel url');
            $table->string('cover', 500)->nullable()->comment('Kapak görsel url');
            $table->string('banner', 500)->nullable()->comment('Banner görsel url');
            $table->string('video', 500)->nullable()->comment('Tanıtım videosu url');
            $table->unsignedInteger('license')->default(0)->comment('Lisans türü (0:Free, 1:Commercial vb.)');
            $table->decimal('price', 19, 4)->default(0)->comment('Satış fiyatı');
            $table->char('currency_code', 3)->default('USD')->comment('Para birimi');
            $table->unsignedBigInteger('tax_class_id')->default(0)->comment('Vergi sınıfı');
            $table->string('version', 50)->default('1.0.0')->comment('Mevcut sürüm');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Ortalama puan (0-5)');
            $table->unsignedInteger('download')->default(0)->comment('İndirilme/Kurulma sayısı');
            $table->string('support_mail', 255)->nullable()->comment('Destek e-posta adresi');
            $table->string('support_website', 500)->nullable()->comment('Destek web sitesi');
            $table->string('support_document', 500)->nullable()->comment('Dokümantasyon url');
            $table->boolean('status')->default(true)->index()->comment('Durum (Aktif/Pasif)');
            $table->timestamp('date_lastupdate')->nullable()->comment('Son güncelleme yayını tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_TRANSLATION, function (Blueprint $table) {
            $table->comment('Uygulama metin çevirileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->index()->comment('Uygulama adı');
            $table->string('summary', 500)->nullable()->comment('Kısa özet');
            $table->string('tag', 500)->nullable()->comment('Etiketler');
            $table->string('keyword', 255)->nullable()->comment('Arama anahtar kelimeleri');
            $table->longText('description')->nullable()->comment('Detaylı HTML açıklama');
            $table->longText('requirement')->nullable()->comment('Gereksinimler');
            $table->string('meta_title', 255)->nullable()->comment('SEO Başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO Açıklama');
            $table->string('meta_keyword', 500)->nullable()->comment('SEO Anahtarlar');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['application_id', 'language_code'], 'idx_app_trans_unique');
            $table->index('deleted_at', 'idx_app_trans_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_IMAGE, function (Blueprint $table) {
            $table->comment('Uygulama ekran görüntüleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->string('file', 500)->comment('Görsel yolu');
            $table->string('description', 500)->nullable()->comment('Görsel açıklaması');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_img_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_FAQ, function (Blueprint $table) {
            $table->comment('Uygulama SSS ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_faq_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->unsignedBigInteger('faq_id')->comment('Soru kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_faq_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_POST, function (Blueprint $table) {
            $table->comment('Uygulama Blog/Haber ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_post_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->unsignedBigInteger('post_id')->comment('Yazı kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_post_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_RELATED, function (Blueprint $table) {
            $table->comment('Benzer uygulama ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_related_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->unsignedBigInteger('related_id')->comment('Benzer uygulama kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_rel_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_CAMPAIGN, function (Blueprint $table) {
            $table->comment('Uygulama kampanya ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_campaign_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->unsignedBigInteger('campaign_id')->comment('Kampanya kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_camp_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_CHANNEL_SERVICES, function (Blueprint $table) {
            $table->comment('Pazaryeri entegrasyon servisleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_channel_service_id')->comment('Servis benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('application_code', 100)->index()->comment('Uygulama Kodu');
            $table->string('channel_service_code', 100)->index()->comment('Servis kodu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_app_ch_serv_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_APPLICATION_CHANNEL_SERVICES);
        Schema::dropIfExists(self::TABLE_APPLICATION_CAMPAIGN);
        Schema::dropIfExists(self::TABLE_APPLICATION_RELATED);
        Schema::dropIfExists(self::TABLE_APPLICATION_POST);
        Schema::dropIfExists(self::TABLE_APPLICATION_FAQ);
        Schema::dropIfExists(self::TABLE_APPLICATION_IMAGE);
        Schema::dropIfExists(self::TABLE_APPLICATION_TRANSLATION);
        Schema::dropIfExists(self::TABLE_APPLICATION);
    }
};
