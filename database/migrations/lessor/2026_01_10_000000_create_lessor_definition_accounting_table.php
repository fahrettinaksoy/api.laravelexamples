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

    // Sipariş Operasyon Tanımlamaları
    private const ORDER_OPERATION_TABLE = 'dfntn_accntng_order_operation';

    private const ORDER_OPERATION_TRANSLATION_TABLE = 'dfntn_accntng_order_operation_translation';

    // Sipariş Durum Tanımlamaları
    private const ORDER_STATUS_TABLE = 'dfntn_accntng_order_status';

    private const ORDER_STATUS_TRANSLATION_TABLE = 'dfntn_accntng_order_status_translation';

    // Fatura Kategori Tanımlamaları
    private const INVOICE_CATEGORY_TABLE = 'dfntn_accntng_invoice_category';

    private const INVOICE_CATEGORY_TRANSLATION_TABLE = 'dfntn_accntng_invoice_category_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm muhasebe tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_accntng_order_operation tablosunu oluştur
        Schema::create(self::ORDER_OPERATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Sipariş süreçlerindeki operasyonları (örn: Sipariş Oluşturuldu, Ödeme Alındı, Kargoya Verildi) tanımlar.');

            $table->bigIncrements('operation_id')->comment('Operasyon için birincil anahtar');
            $table->string('color', 50)->nullable()->comment('Operasyon durumunu temsil eden renk kodu (örn: #RRGGBB)'); // 255 yerine 50
            $table->integer('sort_order')->default(0)->comment('Operasyonların listeleme sırası');
            $table->boolean('status')->default(false)->comment('Operasyonun durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('sort_order', 'idx_dfntn_acc_ord_op_sort_order');
            $table->index('status', 'idx_dfntn_acc_ord_op_status');
        });

        // dfntn_accntng_order_operation_translation tablosunu oluştur
        Schema::create(self::ORDER_OPERATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş operasyon tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('operation_translation_id')->comment('Operasyon çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('operation_id')->comment('İlgili operasyon ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Operasyonun çevrilmiş adı');
            $table->string('description', 500)->nullable()->comment('Operasyonun çevrilmiş açıklaması'); // 255 yerine 500
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('operation_id', 'idx_dfntn_acc_ord_op_trans_op_id');
            $table->index('language_code', 'idx_dfntn_acc_ord_op_trans_lang_code');
            $table->unique(['operation_id', 'language_code'], 'idx_dfntn_acc_ord_op_trans_unique_lang');
        });

        // dfntn_accntng_order_status tablosunu oluştur
        Schema::create(self::ORDER_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişlerin durumlarını (örn: Beklemede, Hazırlanıyor, Tamamlandı) tanımlar.');

            $table->bigIncrements('status_id')->comment('Durum için birincil anahtar');
            $table->unsignedBigInteger('operation_id')->default(0)->comment('Durumun ilişkili olduğu operasyon ID\'si (örn: ödeme beklentisi, kargolama)');
            $table->string('color', 50)->nullable()->comment('Durumu temsil eden renk kodu');
            $table->boolean('status')->default(false)->comment('Durumun genel aktiflik durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('operation_id', 'idx_dfntn_acc_ord_stat_op_id');
            $table->index('status', 'idx_dfntn_acc_ord_stat_status');
        });

        // dfntn_accntng_order_status_translation tablosunu oluştur
        Schema::create(self::ORDER_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sipariş durumu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('status_translation_id')->comment('Durum çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('status_id')->comment('İlgili durum ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Durumun çevrilmiş adı');
            $table->string('description', 500)->nullable()->comment('Durumun çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status_id', 'idx_dfntn_acc_ord_stat_trans_stat_id');
            $table->index('language_code', 'idx_dfntn_acc_ord_stat_trans_lang_code');
            $table->unique(['status_id', 'language_code'], 'idx_dfntn_acc_ord_stat_trans_unique_lang');
        });

        // dfntn_accntng_invoice_category tablosunu oluştur
        Schema::create(self::INVOICE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kategorilerini (örn: Satış, Alış, Gider, Gelir) tanımlar.');

            $table->bigIncrements('category_id')->comment('Kategori için birincil anahtar');
            $table->unsignedBigInteger('parent_id')->default(0)->nullable()->comment('Üst kategoriye referans ID (0 ise ana kategori)');
            $table->string('color', 50)->nullable()->comment('Kategoriyi temsil eden renk kodu');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('parent_id', 'idx_dfntn_acc_inv_cat_parent_id');
            $table->index('status', 'idx_dfntn_acc_inv_cat_status');
        });

        // dfntn_accntng_invoice_category_translation tablosunu oluştur
        Schema::create(self::INVOICE_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kategori tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->string('description', 500)->nullable()->comment('Kategorinin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_dfntn_acc_inv_cat_trans_cat_id');
            $table->index('language_code', 'idx_dfntn_acc_inv_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_dfntn_acc_inv_cat_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::INVOICE_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::INVOICE_CATEGORY_TABLE);
        Schema::dropIfExists(self::ORDER_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ORDER_STATUS_TABLE);
        Schema::dropIfExists(self::ORDER_OPERATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ORDER_OPERATION_TABLE);
    }
};
