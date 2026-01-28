<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bu migration'ın kullanacağı veritabanı bağlantısının adı.
     * Bu özellik tanımlandığında, bu sınıf içindeki tüm Schema işlemleri
     * 'conn_desc' bağlantısı üzerinde otomatik olarak yürütülür.
     */
    protected $connection = 'conn_desc';

    // Tema Tanımlamaları
    private const THEME_TABLE = 'dfntn_dsgn_theme';
    private const THEME_TRANSLATION_TABLE = 'dfntn_dsgn_theme_translation';
    private const THEME_TEMPLATE_TABLE = 'dfntn_dsgn_theme_template'; // Original table name was `template_widget`, renamed for clarity

    // Cihaz Tanımlamaları
    private const DEVICE_TABLE = 'dfntn_dsgn_device';
    private const DEVICE_TRANSLATION_TABLE = 'dfntn_dsgn_device_translation';

    // Pozisyon Tanımlamaları
    private const POSITION_TABLE = 'dfntn_dsgn_position';
    private const POSITION_TRANSLATION_TABLE = 'dfntn_dsgn_position_translation';
    private const POSITION_TEMPLATE_TABLE = 'dfntn_dsgn_position_template';

    // Blok Kategori ve İçerik Tanımlamaları
    private const BLOCK_CATEGORY_TABLE = 'dfntn_dsgn_block_category';
    private const BLOCK_CATEGORY_TRANSLATION_TABLE = 'dfntn_dsgn_block_category_translation';
    private const BLOCK_ITEM_TABLE = 'dfntn_dsgn_block_item';
    private const BLOCK_ITEM_TRANSLATION_TABLE = 'dfntn_dsgn_block_item_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm tasarım tanımlama tablolarını belirtilen 'conn_desc' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // dfntn_dsgn_theme tablosunu oluştur
        Schema::create(self::THEME_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Tasarım temalarını (website temaları) tanımlar.');

            $table->bigIncrements('theme_id')->comment('Tema için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Tema için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Tema görselinin URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Temaların listeleme sırası');
            $table->boolean('status')->default(false)->comment('Temanın durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_dsgn_theme_code'); // Zaten unique, ek indeks belirtildi
            $table->index('sort_order', 'idx_dfntn_dsgn_theme_sort_order');
            $table->index('status', 'idx_dfntn_dsgn_theme_status');
        });

        // dfntn_dsgn_theme_translation tablosunu oluştur
        Schema::create(self::THEME_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tema bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('theme_translation_id')->comment('Tema çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('theme_id')->comment('İlgili tema ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Temanın çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Temanın çevrilmiş kısa özeti'); // 255 yerine 500
            $table->text('description')->nullable()->comment('Temanın çevrilmiş detaylı açıklaması'); // 255 yerine text
            $table->string('keyword', 255)->nullable()->comment('SEO için çevrilmiş anahtar kelimeler');
            $table->string('tag', 255)->nullable()->comment('Temanın çevrilmiş etiketleri (virgülle ayrılmış)');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması'); // 255 yerine 500
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('theme_id', 'idx_dfntn_dsgn_theme_trans_theme_id');
            $table->index('language_code', 'idx_dfntn_dsgn_theme_trans_lang_code');
            $table->unique(['theme_id', 'language_code'], 'idx_dfntn_dsgn_theme_trans_unique_lang');
        });

        // dfntn_dsgn_theme_template tablosunu oluştur
        Schema::create(self::THEME_TEMPLATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Temalara ait ön tanımlı şablonları veya widgetları saklar.');

            $table->bigIncrements('theme_template_id')->comment('Tema şablonu için birincil anahtar'); // Renamed from template_widget_id
            $table->unsignedBigInteger('theme_id')->comment('İlgili tema ID\'si');
            $table->string('widget_type', 100)->nullable()->comment('Şablonun ilişkili olduğu widget tipi (örn: "header", "footer", "sidebar")'); // 255 yerine 100
            $table->string('code', 100)->unique()->comment('Şablon için benzersiz kod'); // 255 yerine 100
            $table->string('name', 255)->comment('Şablonun adı');
            $table->string('thumb', 255)->nullable()->comment('Şablonun küçük resim önizleme görseli');
            $table->string('path', 255)->nullable()->comment('Şablon dosyasının yolu');
            $table->boolean('status')->default(false)->comment('Şablonun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('theme_id', 'idx_dfntn_dsgn_theme_temp_theme_id');
            $table->index('widget_type', 'idx_dfntn_dsgn_theme_temp_widget_type');
            $table->index('code', 'idx_dfntn_dsgn_theme_temp_code'); // Zaten unique, ek indeks belirtildi
            $table->index('status', 'idx_dfntn_dsgn_theme_temp_status');
        });

        // dfntn_dsgn_device tablosunu oluştur
        Schema::create(self::DEVICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Cihaz tiplerini (örn: Masaüstü, Tablet, Mobil) tanımlar.');

            $table->bigIncrements('device_id')->comment('Cihaz için birincil anahtar');
            $table->string('code', 50)->unique()->comment('Cihaz için benzersiz kod'); // 255 yerine 50
            $table->string('prefix', 50)->nullable()->comment('Cihaza özel CSS sınıf öneki (örn: "md:", "lg:")');
            $table->string('icon', 100)->nullable()->comment('Cihazı temsil eden ikon sınıfı veya URL');
            $table->string('dimension', 50)->nullable()->comment('Cihaz ekran boyutları (örn: "768x1024")');
            $table->string('landmark', 50)->nullable()->comment('Cihazın anahtar özelliği/benchmark (örn: "desktop-large")');
            $table->boolean('status')->default(false)->comment('Cihazın durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_dsgn_device_code'); // Zaten unique, ek indeks belirtildi
            $table->index('status', 'idx_dfntn_dsgn_device_status');
        });

        // dfntn_dsgn_device_translation tablosunu oluştur
        Schema::create(self::DEVICE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Cihaz tipi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('device_translation_id')->comment('Cihaz çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('device_id')->comment('İlgili cihaz ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Cihaz tipinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Cihaz tipinin çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('device_id', 'idx_dfntn_dsgn_device_trans_dev_id');
            $table->index('language_code', 'idx_dfntn_dsgn_device_trans_lang_code');
            $table->unique(['device_id', 'language_code'], 'idx_dfntn_dsgn_device_trans_unique_lang');
        });

        // dfntn_dsgn_position tablosunu oluştur
        Schema::create(self::POSITION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım pozisyonlarını (header, footer, sidebar) tanımlar.');

            $table->bigIncrements('position_id')->comment('Pozisyon için birincil anahtar');
            $table->string('type', 50)->nullable()->comment('Pozisyon tipi (örn: "global", "page-specific")'); // 255 yerine 50
            $table->string('code', 100)->unique()->comment('Pozisyon için benzersiz kod (örn: "header", "footer", "content-top")'); // 255 yerine 100
            $table->string('relation', 255)->nullable()->comment('Pozisyonun ilişkili olduğu sayfa veya modül (örn: "blog-post", "product-page")');
            $table->boolean('status')->default(false)->comment('Pozisyonun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_dsgn_pos_code'); // Zaten unique, ek indeks belirtildi
            $table->index('type', 'idx_dfntn_dsgn_pos_type');
            $table->index('status', 'idx_dfntn_dsgn_pos_status');
        });

        // dfntn_dsgn_position_translation tablosunu oluştur
        Schema::create(self::POSITION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım pozisyonu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('position_translation_id')->comment('Pozisyon çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('position_id')->comment('İlgili pozisyon ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Pozisyonun çevrilmiş adı');
            $table->text('description')->nullable()->comment('Pozisyonun çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('position_id', 'idx_dfntn_dsgn_pos_trans_pos_id');
            $table->index('language_code', 'idx_dfntn_dsgn_pos_trans_lang_code');
            $table->unique(['position_id', 'language_code'], 'idx_dfntn_dsgn_pos_trans_unique_lang');
        });

        // dfntn_dsgn_position_template tablosunu oluştur
        Schema::create(self::POSITION_TEMPLATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım pozisyonlarına atanabilecek şablonları veya widgetları saklar.');

            $table->bigIncrements('position_template_id')->comment('Pozisyon şablonu için birincil anahtar');
            $table->unsignedBigInteger('position_id')->comment('İlgili pozisyon ID\'si');
            $table->string('code', 100)->unique()->comment('Şablon için benzersiz kod');
            $table->string('name', 255)->comment('Şablonun adı');
            $table->string('thumb', 255)->nullable()->comment('Şablonun küçük resim önizleme görseli');
            $table->longText('content')->nullable()->comment('Şablonun HTML içeriği veya diğer yapılandırma (JSON)'); // text yerine longText
            $table->integer('sort_order')->default(0)->comment('Şablonların pozisyon içindeki gösterim sırası');
            $table->boolean('status')->default(false)->comment('Şablonun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('position_id', 'idx_dfntn_dsgn_pos_temp_pos_id');
            $table->index('code', 'idx_dfntn_dsgn_pos_temp_code'); // Zaten unique, ek indeks belirtildi
            $table->index('sort_order', 'idx_dfntn_dsgn_pos_temp_sort_order');
            $table->index('status', 'idx_dfntn_dsgn_pos_temp_status');
        });

        // dfntn_dsgn_block_category tablosunu oluştur
        Schema::create(self::BLOCK_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım blok kategorilerini tanımlar (örn: Header Blokları, İçerik Blokları).');

            $table->bigIncrements('category_id')->comment('Blok kategori için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Kategori için benzersiz kod');
            $table->string('name', 255)->comment('Kategorinin adı');
            $table->string('color', 50)->nullable()->comment('Kategoriyi temsil eden renk kodu');
            $table->integer('sort_order')->default(0)->comment('Kategorilerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_dsgn_block_cat_code');
            $table->index('sort_order', 'idx_dfntn_dsgn_block_cat_sort_order');
            $table->index('status', 'idx_dfntn_dsgn_block_cat_status');
        });

        // dfntn_dsgn_block_category_translation tablosunu oluştur
        Schema::create(self::BLOCK_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım blok kategori tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Blok kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili blok kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Kategorinin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_dfntn_dsgn_block_cat_trans_id');
            $table->index('language_code', 'idx_dfntn_dsgn_block_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_dfntn_dsgn_block_cat_trans_unique_lang');
        });

        // dfntn_dsgn_block_item tablosunu oluştur
        Schema::create(self::BLOCK_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım blok öğelerini (önceden tanımlı HTML/CSS bileşenleri) tanımlar.');

            $table->bigIncrements('item_id')->comment('Blok öğesi için birincil anahtar');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Blok öğesinin ait olduğu kategori ID\'si');
            $table->string('code', 100)->unique()->comment('Blok öğesi için benzersiz kod');
            $table->string('name', 255)->comment('Blok öğesinin adı');
            $table->string('thumb', 255)->nullable()->comment('Blok öğesinin küçük resim önizleme görseli');
            $table->longText('content')->nullable()->comment('Blok öğesinin HTML/JavaScript içeriği'); // text yerine longText
            $table->text('style')->nullable()->comment('Blok öğesine özel CSS stilleri (inline veya referans)'); // string yerine text
            $table->integer('sort_order')->default(0)->comment('Blok öğelerinin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Blok öğesinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_dfntn_dsgn_block_item_cat_id');
            $table->index('code', 'idx_dfntn_dsgn_block_item_code'); // Zaten unique, ek indeks belirtildi
            $table->index('sort_order', 'idx_dfntn_dsgn_block_item_sort_order');
            $table->index('status', 'idx_dfntn_dsgn_block_item_status');
        });

        // dfntn_dsgn_block_item_translation tablosunu oluştur
        Schema::create(self::BLOCK_ITEM_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tasarım blok öğesi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('item_translation_id')->comment('Blok öğesi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('item_id')->comment('İlgili blok öğesi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Blok öğesinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Blok öğesinin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('item_id', 'idx_dfntn_dsgn_block_item_trans_id');
            $table->index('language_code', 'idx_dfntn_dsgn_block_item_trans_lang_code');
            $table->unique(['item_id', 'language_code'], 'idx_dfntn_dsgn_block_item_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_desc' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     *
     * @return void
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::BLOCK_ITEM_TRANSLATION_TABLE);
        Schema::dropIfExists(self::BLOCK_ITEM_TABLE);
        Schema::dropIfExists(self::BLOCK_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::BLOCK_CATEGORY_TABLE);

        Schema::dropIfExists(self::POSITION_TEMPLATE_TABLE);
        Schema::dropIfExists(self::POSITION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::POSITION_TABLE);

        Schema::dropIfExists(self::DEVICE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DEVICE_TABLE);

        Schema::dropIfExists(self::THEME_TEMPLATE_TABLE);
        Schema::dropIfExists(self::THEME_TRANSLATION_TABLE);
        Schema::dropIfExists(self::THEME_TABLE);
    }
};
