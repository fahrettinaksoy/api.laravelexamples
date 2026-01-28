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

    // Sıkça Sorulan Sorular (FAQ) Tabloları
    private const FAQ_GROUP_TABLE = 'spprt_faq_group';
    private const FAQ_GROUP_TRANSLATION_TABLE = 'spprt_faq_group_translation';
    private const FAQ_TABLE = 'spprt_faq';
    private const FAQ_TRANSLATION_TABLE = 'spprt_faq_translation';
    private const FAQ_FAQ_GROUP_TABLE = 'spprt_faq_faq_group';

    // Bilgi Bankası (Knowledge Base) Tabloları
    private const KNOWLEDGE_CATEGORY_TABLE = 'spprt_knowledge_category';
    private const KNOWLEDGE_CATEGORY_TRANSLATION_TABLE = 'spprt_knowledge_category_translation';
    private const KNOWLEDGE_ARTICLE_TABLE = 'spprt_knowledge_article';
    private const KNOWLEDGE_ARTICLE_TRANSLATION_TABLE = 'spprt_knowledge_article_translation';
    private const KNOWLEDGE_ARTICLE_CATEGORY_TABLE = 'spprt_knowledge_article_category';
    private const KNOWLEDGE_ARTICLE_IMAGE_TABLE = 'spprt_knowledge_article_image';
    private const KNOWLEDGE_ARTICLE_VIDEO_TABLE = 'spprt_knowledge_article_video';
    private const KNOWLEDGE_ARTICLE_RELATED_TABLE = 'spprt_knowledge_article_related';

    // Geri Bildirim (Feedback) Tablosu
    private const FEEDBACK_TABLE = 'spprt_feedback';

    // Destek Talebi (Ticket) Tabloları
    private const TICKET_TABLE = 'spprt_ticket';
    private const TICKET_MEETING_TABLE = 'spprt_ticket_meeting'; // 'meeting' yerine 'message' veya 'conversation' daha uygun olabilir

    /**
     * Migration'ı çalıştır. Gerekli tüm destek sistemi tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // spprt_faq_group tablosunu oluştur
        Schema::create(self::FAQ_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Sıkça Sorulan Soruların (FAQ) gruplarını tanımlar.');

            $table->bigIncrements('faq_group_id')->comment('FAQ grubu için birincil anahtar');
            $table->string('code', 100)->unique()->comment('FAQ grubu için benzersiz kod'); // 255 yerine 100
            $table->integer('sort_order')->default(0)->comment('FAQ gruplarının listeleme sırası');
            $table->boolean('status')->default(false)->comment('FAQ grubunun durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_faq_group_code');
            $table->index('sort_order', 'idx_spprt_faq_group_sort_order');
            $table->index('status', 'idx_spprt_faq_group_status');
        });

        // spprt_faq_group_translation tablosunu oluştur
        Schema::create(self::FAQ_GROUP_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sıkça Sorulan Sorular (FAQ) grubu tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('faq_group_translation_id')->comment('FAQ grubu çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('faq_group_id')->comment('İlgili FAQ grubu ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('FAQ grubunun çevrilmiş adı');
            $table->text('description')->nullable()->comment('FAQ grubunun çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('faq_group_id', 'idx_spprt_faq_group_trans_id');
            $table->index('language_code', 'idx_spprt_faq_group_trans_lang_code');
            $table->unique(['faq_group_id', 'language_code'], 'idx_spprt_faq_group_trans_unique_lang');
        });

        // spprt_faq tablosunu oluştur
        Schema::create(self::FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sıkça Sorulan Soruların (FAQ) temel bilgilerini saklar.');

            $table->bigIncrements('faq_id')->comment('FAQ için birincil anahtar');
            $table->string('code', 100)->unique()->comment('FAQ için benzersiz kod'); // 255 yerine 100
            $table->integer('sort_order')->default(0)->comment('FAQ\'ların listeleme sırası');
            $table->tinyInteger('membership')->default(0)->comment('FAQ\'un üyelik durumu veya erişim düzeyi (örn: 0: Herkes, 1: Üyeler)');
            $table->boolean('status')->default(false)->comment('FAQ\'un durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_faq_code');
            $table->index('sort_order', 'idx_spprt_faq_sort_order');
            $table->index('membership', 'idx_spprt_faq_membership');
            $table->index('status', 'idx_spprt_faq_status');
        });

        // spprt_faq_translation tablosunu oluştur
        Schema::create(self::FAQ_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sıkça Sorulan Soruların (FAQ) farklı dillere çevirilerini saklar.');

            $table->bigIncrements('faq_translation_id')->comment('FAQ çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('faq_id')->comment('İlgili FAQ ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('question', 500)->comment('FAQ\'un çevrilmiş sorusu'); // 255 yerine 500
            $table->text('answer')->comment('FAQ\'un çevrilmiş cevabı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('faq_id', 'idx_spprt_faq_trans_faq_id');
            $table->index('language_code', 'idx_spprt_faq_trans_lang_code');
            $table->unique(['faq_id', 'language_code'], 'idx_spprt_faq_trans_unique_lang');
        });

        // spprt_faq_faq_group tablosunu oluştur
        Schema::create(self::FAQ_FAQ_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sıkça Sorulan Soruların (FAQ) gruplarla ilişkilerini saklar.');

            $table->bigIncrements('faq_faq_group_id')->comment('FAQ-FAQ Grup ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('faq_id')->comment('İlgili FAQ ID\'si');
            $table->unsignedBigInteger('faq_group_id')->comment('İlgili FAQ grup ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('faq_id', 'idx_spprt_faq_faq_group_faq_id');
            $table->index('faq_group_id', 'idx_spprt_faq_faq_group_group_id');
            $table->unique(['faq_id', 'faq_group_id'], 'idx_spprt_faq_faq_group_unique');
        });

        // spprt_knowledge_category tablosunu oluştur
        Schema::create(self::KNOWLEDGE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası kategorilerini tanımlar.');

            $table->bigIncrements('category_id')->comment('Bilgi bankası kategori için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Kategori için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Kategori görselinin URL veya dosya yolu');
            $table->unsignedBigInteger('parent_id')->default(0)->nullable()->comment('Üst kategoriye referans ID (0 ise ana kategori)');
            $table->unsignedBigInteger('layout_id')->default(0)->nullable()->comment('Kategorinin kullanacağı sayfa düzeni ID\'si');
            $table->integer('sort_order')->default(0)->comment('Kategorilerin listeleme sırası');
            $table->tinyInteger('membership')->default(0)->comment('Kategorinin üyelik durumu veya erişim düzeyi');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_know_cat_code');
            $table->index('parent_id', 'idx_spprt_know_cat_parent_id');
            $table->index('sort_order', 'idx_spprt_know_cat_sort_order');
            $table->index('status', 'idx_spprt_know_cat_status');
        });

        // spprt_knowledge_category_translation tablosunu oluştur
        Schema::create(self::KNOWLEDGE_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası kategori tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kategorinin çevrilmiş kısa özeti');
            $table->string('keyword', 255)->nullable()->comment('Kategorinin SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_spprt_know_cat_trans_cat_id');
            $table->index('language_code', 'idx_spprt_know_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_spprt_know_cat_trans_unique_lang');
        });

        // spprt_knowledge_article tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makalelerinin temel bilgilerini saklar.');

            $table->bigIncrements('article_id')->comment('Makale için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Makale için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Makale ana görselinin URL veya dosya yolu');
            $table->unsignedInteger('viewed')->default(0)->comment('Makalenin görüntülenme sayısı'); // integer yerine unsignedInteger
            $table->unsignedBigInteger('layout_id')->default(0)->nullable()->comment('Makalenin görünüm şablonuna referans ID');
            $table->integer('sort_order')->default(0)->comment('Makalelerin listeleme sırası');
            $table->tinyInteger('membership')->default(0)->comment('Makalenin üyelik durumu veya erişim düzeyi');
            $table->boolean('status')->default(false)->comment('Makalenin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_know_art_code');
            $table->index('viewed', 'idx_spprt_know_art_viewed');
            $table->index('sort_order', 'idx_spprt_know_art_sort_order');
            $table->index('status', 'idx_spprt_know_art_status');
        });

        // spprt_knowledge_article_translation tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makale bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('article_translation_id')->comment('Makale çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('article_id')->comment('İlgili makale ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Makalenin çevrilmiş adı/başlığı');
            $table->string('summary', 500)->nullable()->comment('Makalenin çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Makalenin çevrilmiş detaylı içeriği');
            $table->string('tag', 255)->nullable()->comment('Makalenin çevrilmiş etiketleri (virgülle ayrılmış)');
            $table->string('keyword', 255)->nullable()->comment('Makalenin SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('article_id', 'idx_spprt_know_art_trans_art_id');
            $table->index('language_code', 'idx_spprt_know_art_trans_lang_code');
            $table->unique(['article_id', 'language_code'], 'idx_spprt_know_art_trans_unique_lang');
        });

        // spprt_knowledge_article_category tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makaleleri ile kategori ilişkilerini saklar.');

            $table->bigIncrements('article_category_id')->comment('Makale-kategori ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('article_id')->comment('İlgili makale ID\'si');
            $table->unsignedBigInteger('category_id')->comment('İlgili kategori ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('article_id', 'idx_spprt_know_art_cat_art_id');
            $table->index('category_id', 'idx_spprt_know_art_cat_cat_id');
            $table->unique(['article_id', 'category_id'], 'idx_spprt_know_art_cat_unique');
        });

        // spprt_knowledge_article_image tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makalelerine ait görselleri saklar.');

            $table->bigIncrements('article_image_id')->comment('Makale görseli için birincil anahtar');
            $table->unsignedBigInteger('article_id')->comment('İlgili makale ID\'si');
            $table->string('file', 255)->comment('Görsel dosyasının URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Görsellerin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('article_id', 'idx_spprt_know_art_img_art_id');
            $table->index('sort_order', 'idx_spprt_know_art_img_sort_order');
        });

        // spprt_knowledge_article_video tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makalelerine ait videoları saklar.');

            $table->bigIncrements('article_video_id')->comment('Makale videosu için birincil anahtar');
            $table->unsignedBigInteger('article_id')->comment('İlgili makale ID\'si');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->comment('Video kaynağı tipi (örn: YouTube embed kodu, doğrudan URL)');
            $table->text('content')->nullable()->comment('Video içeriği (URL, embed kodu veya dosya yolu)'); // 255 yerine text
            $table->string('name', 255)->nullable()->comment('Videonun adı veya açıklaması');
            $table->integer('sort_order')->default(0)->comment('Videoların gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('article_id', 'idx_spprt_know_art_vid_art_id');
            $table->index('source', 'idx_spprt_know_art_vid_source');
            $table->index('sort_order', 'idx_spprt_know_art_vid_sort_order');
        });

        // spprt_knowledge_article_related tablosunu oluştur
        Schema::create(self::KNOWLEDGE_ARTICLE_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Bilgi bankası makaleleri arasındaki ilişkiyi (ilgili makaleler) saklar.');

            $table->bigIncrements('article_related_id')->comment('İlgili makale ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('article_id')->comment('Ana makale ID\'si');
            $table->unsignedBigInteger('related_id')->comment('İlgili makale ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('article_id', 'idx_spprt_know_art_rel_art_id');
            $table->index('related_id', 'idx_spprt_know_art_rel_related_id');
            $table->unique(['article_id', 'related_id'], 'idx_spprt_know_art_rel_unique');
        });

        // spprt_feedback tablosunu oluştur
        Schema::create(self::FEEDBACK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kullanıcılardan gelen geri bildirimleri saklar.');

            $table->bigIncrements('feedback_id')->comment('Geri bildirim için birincil anahtar');
            $table->string('code', 100)->unique()->nullable()->comment('Geri bildirim için benzersiz referans kodu');
            $table->string('name', 255)->comment('Geri bildirim gönderen kişinin adı');
            $table->string('email', 255)->nullable()->comment('Geri bildirim gönderen kişinin e-posta adresi');
            $table->string('subject', 255)->comment('Geri bildirimin konusu');
            $table->text('message')->comment('Geri bildirimin mesaj içeriği'); // 255 yerine text
            $table->unsignedBigInteger('assistant_id')->default(0)->nullable()->comment('Geri bildirimle ilgilenen yetkili/destek personeli ID\'si');
            $table->unsignedBigInteger('department_id')->default(0)->nullable()->comment('Geri bildirimin iletildiği departman ID\'si (dfntn_spprt_department)');
            $table->unsignedBigInteger('priority_id')->default(0)->nullable()->comment('Geri bildirimin öncelik seviyesi ID\'si (dfntn_spprt_priority)');
            $table->unsignedTinyInteger('point')->default(0)->nullable()->comment('Geri bildirime verilen puan/değerlendirme (örn: 1-5 arası)'); // integer yerine unsignedTinyInteger
            $table->boolean('read')->default(false)->comment('Geri bildirimin okundu/okunmadı durumu (0: Okunmadı, 1: Okundu)'); // tinyInteger yerine boolean
            $table->unsignedBigInteger('status_id')->comment('Geri bildirimin durumu ID\'si (dfntn_spprt_feedback_status)');
            $table->string('ip_created', 45)->nullable()->comment('Geri bildirimin gönderildiği IP adresi (IPv6 desteği için 45 karakter)'); // 255 yerine 45
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_feed_code');
            $table->index('email', 'idx_spprt_feed_email');
            $table->index('assistant_id', 'idx_spprt_feed_assistant_id');
            $table->index('department_id', 'idx_spprt_feed_dept_id');
            $table->index('priority_id', 'idx_spprt_feed_prio_id');
            $table->index('status_id', 'idx_spprt_feed_status_id');
            $table->index('read', 'idx_spprt_feed_read');
        });

        // spprt_ticket tablosunu oluştur
        Schema::create(self::TICKET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek taleplerini (ticket) saklar.');

            $table->bigIncrements('ticket_id')->comment('Destek talebi için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Destek talebi için benzersiz kod');
            $table->string('subject', 255)->comment('Destek talebinin konusu');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Talebi açan kullanıcı/hesap ID\'si (0 ise misafir)');
            $table->unsignedBigInteger('assistant_id')->default(0)->nullable()->comment('Talebi atanan yetkili/destek personeli ID\'si');
            $table->unsignedBigInteger('department_id')->default(0)->nullable()->comment('Talebin atandığı departman ID\'si (dfntn_spprt_department)');
            $table->unsignedBigInteger('priority_id')->default(0)->nullable()->comment('Talebin öncelik seviyesi ID\'si (dfntn_spprt_priority)');
            $table->unsignedBigInteger('relation_id')->default(0)->nullable()->comment('Talebin ilişkili olduğu modül/kaynak tipi ID\'si (dfntn_spprt_relation)');
            $table->unsignedBigInteger('resource_id')->default(0)->nullable()->comment('Talebin ilişkili olduğu kaynak ID\'si (örn: sipariş ID, ürün ID)');
            $table->unsignedBigInteger('status_id')->comment('Talebin durumu ID\'si (dfntn_spprt_ticket_status)');
            $table->string('ip_modified', 45)->nullable()->comment('Son güncellemeyi yapan IP adresi'); // 255 yerine 45
            $table->string('ip_created', 45)->nullable()->comment('Talebin oluşturulduğu IP adresi'); // 255 yerine 45
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_spprt_tick_code');
            $table->index('account_id', 'idx_spprt_tick_acc_id');
            $table->index('assistant_id', 'idx_spprt_tick_assistant_id');
            $table->index('department_id', 'idx_spprt_tick_dept_id');
            $table->index('priority_id', 'idx_spprt_tick_prio_id');
            $table->index('relation_id', 'idx_spprt_tick_rel_id');
            $table->index('status_id', 'idx_spprt_tick_status_id');
        });

        // spprt_ticket_meeting tablosunu oluştur
        Schema::create(self::TICKET_MEETING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Destek talepleri içindeki mesajlaşmaları/etkileşimleri saklar.');

            $table->bigIncrements('ticket_meeting_id')->comment('Destek talebi mesajı için birincil anahtar');
            $table->unsignedBigInteger('ticket_id')->comment('İlgili destek talebi ID\'si');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Mesajı gönderen kullanıcı/hesap ID\'si (0 ise yetkili/sistem)');
            $table->unsignedBigInteger('assistant_id')->default(0)->nullable()->comment('Mesajı gönderen yetkili/destek personeli ID\'si (eğer hesap değilse)');
            $table->text('message')->comment('Mesaj içeriği');
            $table->boolean('read')->default(false)->comment('Mesajın okundu/okunmadı durumu'); // tinyInteger yerine boolean
            $table->string('ip_created', 45)->nullable()->comment('Mesajın gönderildiği IP adresi'); // 255 yerine 45
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('ticket_id', 'idx_spprt_tick_meet_tick_id');
            $table->index('account_id', 'idx_spprt_tick_meet_acc_id');
            $table->index('assistant_id', 'idx_spprt_tick_meet_assistant_id');
            $table->index('read', 'idx_spprt_tick_meet_read');
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
        Schema::dropIfExists(self::TICKET_MEETING_TABLE);
        Schema::dropIfExists(self::TICKET_TABLE);
        Schema::dropIfExists(self::FEEDBACK_TABLE);

        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_RELATED_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_VIDEO_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_IMAGE_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_CATEGORY_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_ARTICLE_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::KNOWLEDGE_CATEGORY_TABLE);

        Schema::dropIfExists(self::FAQ_FAQ_GROUP_TABLE);
        Schema::dropIfExists(self::FAQ_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FAQ_TABLE);
        Schema::dropIfExists(self::FAQ_GROUP_TRANSLATION_TABLE);
        Schema::dropIfExists(self::FAQ_GROUP_TABLE);
    }
};
