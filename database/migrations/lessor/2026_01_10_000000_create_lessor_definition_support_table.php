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

    // Departman Tanımlamaları
    private const DEPARTMENT_TABLE = 'dfntn_spprt_department';

    private const DEPARTMENT_TRANSLATION_TABLE = 'dfntn_spprt_department_translation';

    // Destek Talebi Durumu Tanımlamaları
    private const TICKET_STATUS_TABLE = 'dfntn_spprt_ticket_status';

    private const TICKET_STATUS_TRANSLATION_TABLE = 'dfntn_spprt_ticket_status_translation';

    // Geri Bildirim Durumu Tanımlamaları
    private const FEEDBACK_STATUS_TABLE = 'dfntn_spprt_feedback_status';

    private const FEEDBACK_STATUS_TRANSLATION_TABLE = 'dfntn_spprt_feedback_status_translation';

    // Öncelik Tanımlamaları
    private const PRIORITY_TABLE = 'dfntn_spprt_priority';

    private const PRIORITY_TRANSLATION_TABLE = 'dfntn_spprt_priority_translation';

    // İlişki (Relation) Tanımlamaları
    private const RELATION_TABLE = 'dfntn_spprt_relation';

    private const RELATION_TRANSLATION_TABLE = 'dfntn_spprt_relation_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm destek tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_spprt_department tablosunu oluştur
        Schema::create(self::DEPARTMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Destek sistemindeki departmanları tanımlar (örn: Teknik Destek, Satış Destek).');

            $table->bigIncrements('department_id')->comment('Departman için birincil anahtar');
            $table->boolean('status')->default(false)->comment('Departmanın durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');
        });

        // dfntn_spprt_department_translation tablosunu oluştur
        Schema::create(self::DEPARTMENT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek departmanı tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('department_translation_id')->comment('Departman çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('department_id')->comment('İlgili departman ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Departmanın çevrilmiş adı');
            $table->text('description')->nullable()->comment('Departmanın çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('department_id', 'idx_dfntn_spprt_dept_trans_id');
            $table->index('language_code', 'idx_dfntn_spprt_dept_trans_lang_code');
            $table->unique(['department_id', 'language_code'], 'idx_dfntn_spprt_dept_trans_unique_lang');
        });

        // dfntn_spprt_ticket_status tablosunu oluştur
        Schema::create(self::TICKET_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talebi durumlarını (örn: Yeni, Cevaplandı, Kapandı) tanımlar.');

            $table->bigIncrements('ticket_status_id')->comment('Destek talebi durumu için birincil anahtar');
            $table->string('color', 50)->nullable()->comment('Durumu temsil eden renk kodu'); // 255 yerine 50
            $table->boolean('status')->default(false)->comment('Durumun aktiflik durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');
        });

        // dfntn_spprt_ticket_status_translation tablosunu oluştur
        Schema::create(self::TICKET_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talebi durumu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('ticket_status_translation_id')->comment('Destek talebi durumu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('ticket_status_id')->comment('İlgili destek talebi durumu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Durumun çevrilmiş adı');
            $table->text('description')->nullable()->comment('Durumun çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('ticket_status_id', 'idx_dfntn_spprt_tick_stat_trans_id');
            $table->index('language_code', 'idx_dfntn_spprt_tick_stat_trans_lang_code');
            $table->unique(['ticket_status_id', 'language_code'], 'idx_dfntn_spprt_tick_stat_trans_unique_lang');
        });

        // dfntn_spprt_feedback_status tablosunu oluştur
        Schema::create(self::FEEDBACK_STATUS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Geri bildirim (feedback) durumlarını (örn: Yeni, Okundu, Çözüldü) tanımlar.');

            $table->bigIncrements('feedback_status_id')->comment('Geri bildirim durumu için birincil anahtar');
            $table->string('color', 50)->nullable()->comment('Durumu temsil eden renk kodu');
            $table->boolean('status')->default(false)->comment('Durumun aktiflik durumu');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');
        });

        // dfntn_spprt_feedback_status_translation tablosunu oluştur
        Schema::create(self::FEEDBACK_STATUS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Geri bildirim durumu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('feedback_status_translation_id')->comment('Geri bildirim durumu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('feedback_status_id')->comment('İlgili geri bildirim durumu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Durumun çevrilmiş adı');
            $table->text('description')->nullable()->comment('Durumun çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('feedback_status_id', 'idx_dfntn_spprt_feed_stat_trans_id');
            $table->index('language_code', 'idx_dfntn_spprt_feed_stat_trans_lang_code');
            $table->unique(['feedback_status_id', 'language_code'], 'idx_dfntn_spprt_feed_stat_trans_unique_lang');
        });

        // dfntn_spprt_priority tablosunu oluştur
        Schema::create(self::PRIORITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talepleri ve geri bildirimler için öncelik seviyelerini (örn: Düşük, Normal, Yüksek) tanımlar.');

            $table->bigIncrements('priority_id')->comment('Öncelik için birincil anahtar');
            $table->boolean('status')->default(false)->comment('Önceliğin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');
        });

        // dfntn_spprt_priority_translation tablosunu oluştur
        Schema::create(self::PRIORITY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Öncelik tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('priority_translation_id')->comment('Öncelik çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('priority_id')->comment('İlgili öncelik ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Önceliğin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Önceliğin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('priority_id', 'idx_dfntn_spprt_prio_trans_id');
            $table->index('language_code', 'idx_dfntn_spprt_prio_trans_lang_code');
            $table->unique(['priority_id', 'language_code'], 'idx_dfntn_spprt_prio_trans_unique_lang');
        });

        // dfntn_spprt_relation tablosunu oluştur
        Schema::create(self::RELATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek taleplerinin ilişkili olabileceği modül yollarını (örn: product, order) tanımlar.');

            $table->bigIncrements('relation_id')->comment('İlişki için birincil anahtar');
            $table->string('path', 255)->unique()->comment('İlişki yolunun kodu (örn: orders/view/{id})'); // 255 yerine 255
            $table->boolean('status')->default(false)->comment('İlişki tipinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('path', 'idx_dfntn_spprt_rel_path'); // Zaten unique, ek index belirtildi
            $table->index('status', 'idx_dfntn_spprt_rel_status');
        });

        // dfntn_spprt_relation_translation tablosunu oluştur
        Schema::create(self::RELATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlişki tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('relation_translation_id')->comment('İlişki çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('relation_id')->comment('İlgili ilişki ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('İlişkinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('İlişkinin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('relation_id', 'idx_dfntn_spprt_rel_trans_id');
            $table->index('language_code', 'idx_dfntn_spprt_rel_trans_lang_code');
            $table->unique(['relation_id', 'language_code'], 'idx_dfntn_spprt_rel_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::RELATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::RELATION_TABLE);

        Schema::dropIfExists(self::PRIORITY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRIORITY_TABLE);

        Schema::dropIfExists(self::FEEDBACK_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FEEDBACK_STATUS_TABLE);

        Schema::dropIfExists(self::TICKET_STATUS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::TICKET_STATUS_TABLE);

        Schema::dropIfExists(self::DEPARTMENT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::DEPARTMENT_TABLE);
    }
};
