<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bu migration'ın kullanacağı veritabanı bağlantısının adı.
     * Bu özellik tanımlandığında, bu sınıf içindeki tüm Schema işlemleri
     * 'conn_lsr' bağlantısı üzerinde otomatik olarak yürütülür.
     */
    protected $connection = 'conn_lsr';

    // Form Tabloları
    private const FORM_TABLE = 'wbst_form';
    private const FORM_INCOMING_TABLE = 'wbst_form_incoming';
    private const FORM_TRANSLATION_TABLE = 'wbst_form_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm web sitesi form tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // wbst_form tablosunu oluştur
        Schema::create(self::FORM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Web sitesi formlarının (örn: iletişim formu, abonelik formu) temel bilgilerini saklar.');

            $table->bigIncrements('form_id')->comment('Form için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Form için benzersiz kod'); // 255 yerine 100
            $table->string('name', 255)->comment('Formun adı (dahili kullanım için)');
            $table->text('html')->nullable()->comment('Formun HTML yapısı veya şablon yolu'); // 255 yerine text
            $table->text('send')->nullable()->comment('Form gönderimi sonrası yapılacak işlemler (örn: e-posta şablonu, yönlendirme URL\'si, JSON)');
            $table->longText('content')->nullable()->comment('Formun JSON tabanlı alan tanımlamaları veya diğer içerik ayarları'); // text yerine longText
            $table->boolean('status')->default(false)->comment('Formun durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_wbst_form_code');
            $table->index('status', 'idx_wbst_form_status');
        });

        // wbst_form_incoming tablosunu oluştur
        Schema::create(self::FORM_INCOMING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Web sitesi formları aracılığıyla gelen verileri saklar.');

            $table->bigIncrements('form_incoming_id')->comment('Gelen form kaydı için birincil anahtar');
            $table->unsignedBigInteger('form_id')->comment('İlgili form ID\'si');
            $table->longText('content')->comment('Gönderilen form verilerinin JSON formatı'); // text yerine longText
            $table->string('ip_address', 45)->nullable()->comment('Formu gönderen kullanıcının IP adresi (IPv6 desteği için 45 karakter)'); // 255 yerine 45
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('form_id', 'idx_wbst_form_inc_form_id');
        });

        // wbst_form_translation tablosunu oluştur
        Schema::create(self::FORM_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Form metinlerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('form_translation_id')->comment('Form çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('form_id')->comment('İlgili form ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('title', 255)->comment('Formun çevrilmiş başlığı');
            $table->text('description')->nullable()->comment('Formun çevrilmiş açıklaması');
            $table->string('button', 255)->nullable()->comment('Form gönderme butonunun çevrilmiş metni');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('form_id', 'idx_wbst_form_trans_form_id');
            $table->index('language_code', 'idx_wbst_form_trans_lang_code');
            $table->unique(['form_id', 'language_code'], 'idx_wbst_form_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     *
     * @return void
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::FORM_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FORM_INCOMING_TABLE);
        Schema::dropIfExists(self::FORM_TABLE);
    }
};
