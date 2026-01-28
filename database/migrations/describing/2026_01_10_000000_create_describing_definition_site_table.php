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

    // URL Tanımlamaları
    private const SITE_URL_TABLE = 'dfntn_site_url';
    private const SITE_URL_TRANSLATION_TABLE = 'dfntn_site_url_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm site URL tanımlama tablolarını belirtilen 'conn_desc' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // dfntn_site_url tablosunu oluştur
        Schema::create(self::SITE_URL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Site genelinde kullanılacak URL kodlarını ve yönlendirme (routing) bilgilerini tanımlar.');

            $table->bigIncrements('url_id')->comment('URL tanımı için birincil anahtar');
            $table->string('code', 255)->unique()->comment('URL için benzersiz kod (örn: "home", "about-us", "product-detail")');
            $table->string('route', 255)->comment('İlgili Laravel rotası veya dahili yönlendirme yolu');
            $table->unsignedBigInteger('slug_id')->default(0)->nullable()->comment('URL\'in ilişkili olduğu kaynak ID\'si (örn: ürün ID, kategori ID, blog yazısı ID). 0 ise doğrudan bir slug\'a bağlı değil.'); // integer yerine unsignedBigInteger, nullable
            $table->string('module', 100)->nullable()->comment('URL\'in hangi modüle ait olduğu (örn: "catalog", "blog", "page")'); // 255 yerine 100
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_site_url_code'); // Zaten unique, ek indeks belirtildi
            $table->index('route', 'idx_dfntn_site_url_route');
            $table->index('slug_id', 'idx_dfntn_site_url_slug_id');
            $table->index('module', 'idx_dfntn_site_url_module');
        });

        // dfntn_site_url_translation tablosunu oluştur
        Schema::create(self::SITE_URL_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Site URL\'lerinin farklı dillere çevrilmiş SEO dostu anahtar kelimelerini (slug) saklar.');

            $table->bigIncrements('url_translation_id')->comment('URL çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('url_id')->comment('İlgili URL tanımı ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('keyword', 255)->unique()->comment('URL\'in SEO dostu anahtar kelimesi (slug), dilde benzersiz olmalı'); // 255
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('url_id', 'idx_dfntn_site_url_trans_url_id');
            $table->index('language_code', 'idx_dfntn_site_url_trans_lang_code');
            $table->unique(['url_id', 'language_code'], 'idx_dfntn_site_url_trans_unique_url_lang'); // url_id ve language_code kombinasyonu benzersiz olmalı
            $table->index('keyword', 'idx_dfntn_site_url_trans_keyword'); // Zaten unique, ek indeks belirtildi
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
        Schema::dropIfExists(self::SITE_URL_TRANSLATION_TABLE);
        Schema::dropIfExists(self::SITE_URL_TABLE);
    }
};
