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

    private const TABLE_LAYOUT = 'site_layout';

    private const TABLE_LAYOUT_WIDGET = 'site_layout_widget';

    private const TABLE_BANNER = 'site_banner';

    private const TABLE_BANNER_ITEM = 'site_banner_item';

    private const TABLE_MENU = 'site_menu';

    private const TABLE_MENU_ITEM = 'site_menu_item';

    private const TABLE_MENU_ITEM_TRANSLATION = 'site_menu_item_translation';

    private const TABLE_URL = 'site_url';

    private const TABLE_URL_TRANSLATION = 'site_url_translation';

    private const TABLE_FORM = 'site_form';

    private const TABLE_FORM_TRANSLATION = 'site_form_translation';

    private const TABLE_FORM_INCOMING = 'site_form_incoming';

    public function up(): void
    {
        Schema::create(self::TABLE_LAYOUT, function (Blueprint $table) {
            $table->comment('Site sayfa düzenleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('layout_id')->comment('Düzen benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Düzen kodu');
            $table->string('name', 255)->comment('Düzen adı');
            $table->string('path', 255)->nullable()->comment('Dosya yolu veya route deseni');
            $table->text('summary')->nullable()->comment('Özet açıklama');
            $table->text('position')->nullable()->comment('Pozisyon ayarları (JSON)');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_layout_soft_delete');
        });

        Schema::create(self::TABLE_LAYOUT_WIDGET, function (Blueprint $table) {
            $table->comment('Düzen-Widget ilişki ve yerleşim tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('layout_widget_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('layout_id')->comment('Düzen kimliği');
            $table->string('position', 100)->comment('Bölge/Pozisyon adı (header, footer, left_column vb.)');
            $table->longText('widgets')->nullable()->comment('Widget yapılandırması ve sıralaması (JSON)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['layout_id', 'position'], 'idx_layout_widget_pos');
            $table->index('deleted_at', 'idx_layout_widget_soft_delete');
        });

        Schema::create(self::TABLE_BANNER, function (Blueprint $table) {
            $table->comment('Banner grubu ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('banner_id')->comment('Banner benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Banner grubu kodu');
            $table->string('name', 255)->comment('Banner grubu adı');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_banner_soft_delete');
        });

        Schema::create(self::TABLE_BANNER_ITEM, function (Blueprint $table) {
            $table->comment('Banner elemanları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('banner_item_id')->comment('Banner elemanı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('banner_id')->comment('Banner grubu kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('title', 255)->nullable()->comment('Başlık 1');
            $table->string('title2', 255)->nullable()->comment('Başlık 2');
            $table->string('title3', 255)->nullable()->comment('Başlık 3');
            $table->text('summary')->nullable()->comment('Özet/Açıklama');
            $table->string('image', 500)->comment('Görsel yolu');
            $table->string('mobile_image', 500)->nullable()->comment('Mobil görsel yolu');
            $table->string('link', 500)->nullable()->comment('Yönlendirme linki 1');
            $table->string('link2', 500)->nullable()->comment('Yönlendirme linki 2');
            $table->string('link3', 500)->nullable()->comment('Yönlendirme linki 3');
            $table->string('button', 100)->nullable()->comment('Buton metni 1');
            $table->string('button2', 100)->nullable()->comment('Buton metni 2');
            $table->string('button3', 100)->nullable()->comment('Buton metni 3');
            $table->json('display')->nullable()->comment('Görüntüleme ayarları');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['banner_id', 'language_code'], 'idx_banner_item_lang');
            $table->index('deleted_at', 'idx_banner_item_soft_delete');
        });

        Schema::create(self::TABLE_MENU, function (Blueprint $table) {
            $table->comment('Menü grupları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('menu_id')->comment('Menü benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Menü kodu (header, footer vb.)');
            $table->string('type', 100)->nullable()->comment('Menü tipi');
            $table->string('name', 255)->comment('Menü adı');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_menu_soft_delete');
        });

        Schema::create(self::TABLE_MENU_ITEM, function (Blueprint $table) {
            $table->comment('Menü elemanları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('menu_item_id')->comment('Eleman benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('menu_id')->comment('Menü grubu kimliği');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('Üst eleman kimliği');
            $table->enum('type', ['url', 'module', 'category', 'product', 'page'])->default('url')->comment('Eleman tipi');
            $table->string('module_action', 255)->nullable()->comment('Modül aksiyonu');
            $table->unsignedBigInteger('module_id')->default(0)->comment('İlişkili modül kayıt kimliği');
            $table->string('route', 255)->nullable()->comment('Rota adı');
            $table->string('query', 255)->nullable()->comment('URL parametreleri');
            $table->string('target', 50)->default('_self')->comment('Hedef pencere (_self, _blank)');
            $table->string('icon', 100)->nullable()->comment('İkon sınıfı/kodu');
            $table->string('image', 500)->nullable()->comment('Görsel yolu');
            $table->string('id_name', 100)->nullable()->comment('HTML ID özniteliği');
            $table->string('class_name', 100)->nullable()->comment('HTML Class özniteliği');
            $table->text('style')->nullable()->comment('Özel CSS stilleri');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['menu_id', 'parent_id', 'sort_order'], 'idx_menu_item_structure');
            $table->index('deleted_at', 'idx_menu_item_soft_delete');
        });

        Schema::create(self::TABLE_MENU_ITEM_TRANSLATION, function (Blueprint $table) {
            $table->comment('Menü elemanı çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('menu_item_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('menu_item_id')->comment('Menü elemanı kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Görünen ad');
            $table->string('summary', 500)->nullable()->comment('Açıklama/Tooltip');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['menu_item_id', 'language_code'], 'idx_menu_item_lang_unique');
            $table->index('deleted_at', 'idx_menu_item_trans_soft_delete');
        });

        Schema::create(self::TABLE_URL, function (Blueprint $table) {
            $table->comment('SEO URL (Slug) yönetim tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('url_id')->comment('URL benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Benzersiz referans kodu');
            $table->string('module_type', 100)->comment('Modül tipi (product, category, page vb.)');
            $table->unsignedBigInteger('module_id')->default(0)->index()->comment('İlişkili kayıt kimliği');
            $table->string('module_controller', 255)->comment('Controller sınıfı');
            $table->string('module_action', 255)->comment('Controller metodu');
            $table->string('module_query', 255)->nullable()->comment('Ek sorgu parametreleri');
            $table->string('module_pattern', 255)->nullable()->comment('Regex deseni');
            $table->boolean('is_locked')->default(false)->comment('Düzenlemeye kilitli mi?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['module_type', 'module_id'], 'idx_url_module_lookup');
            $table->index('deleted_at', 'idx_url_soft_delete');
        });

        Schema::create(self::TABLE_URL_TRANSLATION, function (Blueprint $table) {
            $table->comment('SEO URL çeviri ve anahtar kelime tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('url_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('url_id')->comment('URL kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('keyword', 255)->index()->comment('SEO anahtar kelimesi (Slug)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['url_id', 'language_code'], 'idx_url_lang_unique');
            $table->index(['keyword', 'language_code'], 'idx_url_keyword_lookup');
            $table->index('deleted_at', 'idx_url_trans_soft_delete');
        });

        Schema::create(self::TABLE_FORM, function (Blueprint $table) {
            $table->comment('Form oluşturucu ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('form_id')->comment('Form benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Form kodu');
            $table->string('name', 255)->comment('Form dahili adı');
            $table->text('html')->nullable()->comment('Özel HTML şablonu');
            $table->text('send_to')->nullable()->comment('Bildirim gönderilecek e-posta adresleri');
            $table->longText('content')->nullable()->comment('Form yapısı (JSON)');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_form_soft_delete');
        });

        Schema::create(self::TABLE_FORM_TRANSLATION, function (Blueprint $table) {
            $table->comment('Form çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('form_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('form_id')->comment('Form kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('title', 255)->comment('Görünen başlık');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->string('button_text', 100)->nullable()->comment('Gönder butonu metni');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['form_id', 'language_code'], 'idx_form_lang_unique');
            $table->index('deleted_at', 'idx_form_trans_soft_delete');
        });

        Schema::create(self::TABLE_FORM_INCOMING, function (Blueprint $table) {
            $table->comment('Gelen form başvuruları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('form_incoming_id')->comment('Başvuru benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('form_id')->comment('Form kimliği');
            $table->longText('content')->comment('Gönderilen veriler (JSON)');
            $table->ipAddress('ip_address')->nullable()->comment('Gönderen IP adresi');
            $table->string('user_agent', 500)->nullable()->comment('Tarayıcı bilgisi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('form_id', 'idx_form_incoming_form');
            $table->index('deleted_at', 'idx_form_incoming_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_FORM_INCOMING);
        Schema::dropIfExists(self::TABLE_FORM_TRANSLATION);
        Schema::dropIfExists(self::TABLE_FORM);
        Schema::dropIfExists(self::TABLE_URL_TRANSLATION);
        Schema::dropIfExists(self::TABLE_URL);
        Schema::dropIfExists(self::TABLE_MENU_ITEM_TRANSLATION);
        Schema::dropIfExists(self::TABLE_MENU_ITEM);
        Schema::dropIfExists(self::TABLE_MENU);
        Schema::dropIfExists(self::TABLE_BANNER_ITEM);
        Schema::dropIfExists(self::TABLE_BANNER);
        Schema::dropIfExists(self::TABLE_LAYOUT_WIDGET);
        Schema::dropIfExists(self::TABLE_LAYOUT);
    }
};
