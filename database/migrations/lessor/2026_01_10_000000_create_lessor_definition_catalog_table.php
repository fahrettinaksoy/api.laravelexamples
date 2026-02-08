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

    // Ürün Tekrarlayan Tip Tanımlamaları
    private const PRODUCT_RECURRING_TYPE_TABLE = 'dfntn_ctlg_prdct_recurring_type';

    private const PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_recurring_type_translation';

    // Ürün Tipi Tanımlamaları
    private const PRODUCT_TYPE_TABLE = 'dfntn_ctlg_prdct_type';

    private const PRODUCT_TYPE_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_type_translation';

    // Ürün Durum Tanımlamaları (Condition)
    private const PRODUCT_CONDITION_TABLE = 'dfntn_ctlg_prdct_condition';

    private const PRODUCT_CONDITION_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_condition_translation';

    // Ürün Stoksuz Satış Tanımlamaları (Stockless)
    private const PRODUCT_STOCKLESS_TABLE = 'dfntn_ctlg_prdct_stockless';

    private const PRODUCT_STOCKLESS_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_stockless_translation';

    // Ürün Alan Tipi Tanımlamaları (Field Type)
    private const PRODUCT_FIELD_TYPE_TABLE = 'dfntn_ctlg_prdct_field_type';

    private const PRODUCT_FIELD_TYPE_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_field_type_translation';

    // Ürün Nitelik (Attribute) Şablonları, Grupları ve Değişkenleri
    private const PRODUCT_ATTRIBUTE_TEMPLATE_TABLE = 'dfntn_ctlg_prdct_attribute_template';

    private const PRODUCT_ATTRIBUTE_GROUP_TABLE = 'dfntn_ctlg_prdct_attribute_group';

    private const PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_attribute_group_translation';

    private const PRODUCT_ATTRIBUTE_VARIABLE_TABLE = 'dfntn_ctlg_prdct_attribute_variable';

    private const PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE = 'dfntn_ctlg_prdct_attribute_variable_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm katalog ürün tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_ctlg_prdct_recurring_type tablosunu oluştur
        Schema::create(self::PRODUCT_RECURRING_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Ürünler için tekrarlayan ödeme (abonelik) tiplerini tanımlar.');

            $table->bigIncrements('recurring_type_id')->comment('Tekrarlayan tip için birincil anahtar');
            $table->string('code', 50)->unique()->comment('Tekrarlayan tip için benzersiz kod (örn: monthly, yearly)');
            $table->integer('duration')->default(0)->comment('Tekrarlayan ödemenin süresi (örn: 12 ay)');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->comment('Tekrarlayan ödeme sıklığı');
            $table->integer('cycle')->default(0)->comment('Tekrarlayan ödeme döngü sayısı (örn: 0 sınırsız, 5 defa)');
            $table->integer('sort_order')->default(0)->comment('Tekrarlayan tiplerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Tekrarlayan tipinin durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean

            $table->decimal('trial_price', 19, 4)->default(0.0000)->comment('Deneme süresi için fiyat'); // 19,2 yerine 19,4
            $table->integer('trial_duration')->default(0)->comment('Deneme süresinin uzunluğu');
            $table->enum('trial_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->comment('Deneme süresi sıklığı');
            $table->integer('trial_cycle')->default(0)->comment('Deneme süresi döngü sayısı');
            $table->boolean('trial_status')->default(false)->comment('Deneme süresi aktif mi?'); // tinyInteger yerine boolean

            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_ctlg_prdct_rec_type_code');
            $table->index('status', 'idx_dfntn_ctlg_prdct_rec_type_status');
            $table->index('sort_order', 'idx_dfntn_ctlg_prdct_rec_type_sort_order');
        });

        // dfntn_ctlg_prdct_recurring_type_translation tablosunu oluştur
        Schema::create(self::PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün tekrarlayan tip tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('recurring_type_translation_id')->comment('Tekrarlayan tip çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('recurring_type_id')->comment('İlgili tekrarlayan tip ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Tekrarlayan tipin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Tekrarlayan tipin çevrilmiş kısa özeti'); // 255 yerine 500
            $table->text('description')->nullable()->comment('Tekrarlayan tipin çevrilmiş detaylı açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('recurring_type_id', 'idx_dfntn_ctlg_prdct_rec_type_trans_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_rec_type_trans_lang_code');
            $table->unique(['recurring_type_id', 'language_code'], 'idx_dfntn_ctlg_prdct_rec_type_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_type tablosunu oluştur
        Schema::create(self::PRODUCT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün tiplerini (örn: Dijital Ürün, Fiziksel Ürün, Hizmet) tanımlar.');

            $table->bigIncrements('type_id')->comment('Ürün tipi için birincil anahtar');
            $table->string('icon', 100)->nullable()->comment('Ürün tipini temsil eden ikon sınıfı veya URL'); // 255 yerine 100
            $table->string('color', 50)->nullable()->comment('Ürün tipini temsil eden renk kodu');
            $table->integer('sort_order')->default(0)->comment('Ürün tiplerinin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Ürün tipinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('sort_order', 'idx_dfntn_ctlg_prdct_type_sort_order');
            $table->index('status', 'idx_dfntn_ctlg_prdct_type_status');
        });

        // dfntn_ctlg_prdct_type_translation tablosunu oluştur
        Schema::create(self::PRODUCT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün tipi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('type_translation_id')->comment('Ürün tipi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('type_id')->comment('İlgili ürün tipi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Ürün tipinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Ürün tipinin çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type_id', 'idx_dfntn_ctlg_prdct_type_trans_type_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_type_trans_lang_code');
            $table->unique(['type_id', 'language_code'], 'idx_dfntn_ctlg_prdct_type_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_condition tablosunu oluştur
        Schema::create(self::PRODUCT_CONDITION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlerin durumlarını (örn: Yeni, İkinci El, Yenilenmiş) tanımlar.');

            $table->bigIncrements('condition_id')->comment('Ürün durumu için birincil anahtar');
            $table->integer('sort_order')->default(0)->comment('Durumların listeleme sırası'); // string yerine integer
            $table->boolean('status')->default(false)->comment('Durumun aktiflik durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('sort_order', 'idx_dfntn_ctlg_prdct_cond_sort_order');
            $table->index('status', 'idx_dfntn_ctlg_prdct_cond_status');
        });

        // dfntn_ctlg_prdct_condition_translation tablosunu oluştur
        Schema::create(self::PRODUCT_CONDITION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün durumu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('condition_translation_id')->comment('Ürün durumu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('condition_id')->comment('İlgili ürün durumu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Durumun çevrilmiş adı');
            $table->text('description')->nullable()->comment('Durumun çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('condition_id', 'idx_dfntn_ctlg_prdct_cond_trans_cond_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_cond_trans_lang_code');
            $table->unique(['condition_id', 'language_code'], 'idx_dfntn_ctlg_prdct_cond_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_stockless tablosunu oluştur
        Schema::create(self::PRODUCT_STOCKLESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stokta olmayan ürünlerin satış politikalarını (örn: ön sipariş, temin edilecek) tanımlar.');

            $table->bigIncrements('stockless_id')->comment('Stoksuz satış durumu için birincil anahtar');
            $table->string('color', 50)->nullable()->comment('Durumu temsil eden renk kodu');
            $table->boolean('status')->default(false)->comment('Stoksuz satış durumunun aktiflik durumu');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status', 'idx_dfntn_ctlg_prdct_stockless_status');
        });

        // dfntn_ctlg_prdct_stockless_translation tablosunu oluştur
        Schema::create(self::PRODUCT_STOCKLESS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Stoksuz satış durumu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('stockless_translation_id')->comment('Stoksuz satış durumu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('stockless_id')->comment('İlgili stoksuz satış durumu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Stoksuz satış durumunun çevrilmiş adı');
            $table->text('description')->nullable()->comment('Stoksuz satış durumunun çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('stockless_id', 'idx_dfntn_ctlg_prdct_stless_trans_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_stless_trans_lang_code');
            $table->unique(['stockless_id', 'language_code'], 'idx_dfntn_ctlg_prdct_stless_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_field_type tablosunu oluştur
        Schema::create(self::PRODUCT_FIELD_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler için özel alan tiplerini (örn: metin, sayı, tarih, dosya) tanımlar.');

            $table->bigIncrements('field_type_id')->comment('Ürün özel alan tipi için birincil anahtar');
            $table->boolean('status')->default(false)->comment('Alan tipinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status', 'idx_dfntn_ctlg_prdct_field_type_status');
        });

        // dfntn_ctlg_prdct_field_type_translation tablosunu oluştur
        Schema::create(self::PRODUCT_FIELD_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özel alan tipi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('field_type_translation_id')->comment('Ürün özel alan tipi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('field_type_id')->comment('İlgili ürün özel alan tipi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Alan tipinin çevrilmiş adı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('field_type_id', 'idx_dfntn_ctlg_prdct_field_type_trans_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_field_type_trans_lang_code');
            $table->unique(['field_type_id', 'language_code'], 'idx_dfntn_ctlg_prdct_field_type_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_attribute_template tablosunu oluştur
        Schema::create(self::PRODUCT_ATTRIBUTE_TEMPLATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün nitelik (attribute) şablonlarını tanımlar.');

            $table->bigIncrements('attribute_template_id')->comment('Nitelik şablonu için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Nitelik şablonu için benzersiz kod');
            $table->string('name', 255)->comment('Nitelik şablonunun adı');
            $table->string('description', 500)->nullable()->comment('Nitelik şablonunun açıklaması');
            $table->boolean('status')->default(false)->comment('Nitelik şablonunun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_ctlg_prdct_attr_temp_code');
            $table->index('status', 'idx_dfntn_ctlg_prdct_attr_temp_status');
        });

        // dfntn_ctlg_prdct_attribute_group tablosunu oluştur
        Schema::create(self::PRODUCT_ATTRIBUTE_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün nitelik (attribute) gruplarını tanımlar.');

            $table->bigIncrements('attribute_group_id')->comment('Nitelik grubu için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Nitelik grubu için benzersiz kod');
            $table->unsignedBigInteger('attribute_template_id')->default(0)->nullable()->comment('İlgili nitelik şablonu ID\'si');
            $table->integer('sort_order')->default(0)->comment('Nitelik gruplarının listeleme sırası');
            $table->boolean('status')->default(false)->comment('Nitelik grubunun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_ctlg_prdct_attr_group_code');
            $table->index('attribute_template_id', 'idx_dfntn_ctlg_prdct_attr_group_temp_id');
            $table->index('sort_order', 'idx_dfntn_ctlg_prdct_attr_group_sort_order');
            $table->index('status', 'idx_dfntn_ctlg_prdct_attr_group_status');
        });

        // dfntn_ctlg_prdct_attribute_group_translation tablosunu oluştur
        Schema::create(self::PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün nitelik grubu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('attribute_group_translation_id')->comment('Nitelik grubu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('attribute_group_id')->comment('İlgili nitelik grubu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Nitelik grubunun çevrilmiş adı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('attribute_group_id', 'idx_dfntn_ctlg_prdct_attr_group_trans_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_attr_group_trans_lang_code');
            $table->unique(['attribute_group_id', 'language_code'], 'idx_dfntn_ctlg_prdct_attr_group_trans_unique_lang');
        });

        // dfntn_ctlg_prdct_attribute_variable tablosunu oluştur
        Schema::create(self::PRODUCT_ATTRIBUTE_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün nitelik (attribute) değişkenlerini (örn: Renk: Kırmızı, Mavi) tanımlar.');

            $table->bigIncrements('attribute_variable_id')->comment('Nitelik değişkeni için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Nitelik değişkeni için benzersiz kod');
            $table->unsignedBigInteger('attribute_group_id')->default(0)->comment('İlgili nitelik grubu ID\'si');
            $table->string('type', 50)->comment('Nitelik değişkeni tipi (örn: text, color, image, number)');
            $table->integer('sort_order')->default(0)->comment('Nitelik değişkenlerinin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Nitelik değişkeninin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_ctlg_prdct_attr_var_code');
            $table->index('attribute_group_id', 'idx_dfntn_ctlg_prdct_attr_var_group_id');
            $table->index('type', 'idx_dfntn_ctlg_prdct_attr_var_type');
            $table->index('sort_order', 'idx_dfntn_ctlg_prdct_attr_var_sort_order');
            $table->index('status', 'idx_dfntn_ctlg_prdct_attr_var_status');
        });

        // dfntn_ctlg_prdct_attribute_variable_translation tablosunu oluştur
        Schema::create(self::PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün nitelik değişkeni tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('attribute_variable_translation_id')->comment('Nitelik değişkeni çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('attribute_variable_id')->comment('İlgili nitelik değişkeni ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Nitelik değişkeninin çevrilmiş adı/değeri');
            $table->string('description', 500)->nullable()->comment('Nitelik değişkeninin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('attribute_variable_id', 'idx_dfntn_ctlg_prdct_attr_var_trans_id');
            $table->index('language_code', 'idx_dfntn_ctlg_prdct_attr_var_trans_lang_code');
            $table->unique(['attribute_variable_id', 'language_code'], 'idx_dfntn_ctlg_prdct_attr_var_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_VARIABLE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_VARIABLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_GROUP_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TEMPLATE_TABLE);

        Schema::dropIfExists(self::PRODUCT_FIELD_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TYPE_TABLE);

        Schema::dropIfExists(self::PRODUCT_STOCKLESS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_STOCKLESS_TABLE);

        Schema::dropIfExists(self::PRODUCT_CONDITION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_CONDITION_TABLE);

        Schema::dropIfExists(self::PRODUCT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_TYPE_TABLE);

        Schema::dropIfExists(self::PRODUCT_RECURRING_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_RECURRING_TYPE_TABLE);
    }
};
