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

    private const TABLE_FEEDBACK = 'spprt_feedback';

    private const TABLE_TICKET = 'spprt_ticket';

    private const TABLE_TICKET_MESSAGE = 'spprt_ticket_message';

    private const TABLE_FAQ = 'spprt_faq';

    private const TABLE_FAQ_TRANSLATION = 'spprt_faq_translation';

    private const TABLE_FAQ_GROUP = 'spprt_faq_group_relation'; // Pivot table for FAQ <-> Group

    private const TABLE_ARTICLE = 'spprt_knowledge_article';

    private const TABLE_ARTICLE_TRANSLATION = 'spprt_knowledge_article_translation';

    private const TABLE_ARTICLE_CATEGORY = 'spprt_knowledge_article_category';

    private const TABLE_ARTICLE_IMAGE = 'spprt_knowledge_article_image';

    private const TABLE_ARTICLE_VIDEO = 'spprt_knowledge_article_video';

    private const TABLE_ARTICLE_RELATED = 'spprt_knowledge_article_related';

    public function up(): void
    {
        Schema::create(self::TABLE_FEEDBACK, function (Blueprint $table) {
            $table->comment('Geri bildirim tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('feedback_id')->comment('Geri bildirim benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Geri bildirim kodu');
            $table->string('name', 255)->comment('Gönderen adı');
            $table->string('email', 255)->index()->comment('Gönderen e-posta adresi');
            $table->string('subject', 255)->comment('Konu');
            $table->text('message')->comment('Mesaj içeriği');
            $table->unsignedBigInteger('account_id')->default(0)->comment('Varsa müşteri hesabı kimliği');
            $table->unsignedBigInteger('assistant_id')->default(0)->comment('Atanan asistan/personel kimliği');
            $table->unsignedInteger('department_id')->default(0)->comment('Departman kimliği');
            $table->unsignedInteger('priority_id')->default(0)->comment('Öncelik kimliği');
            $table->integer('point')->default(0)->comment('Değerlendirme puanı');
            $table->boolean('is_read')->default(false)->comment('Okundu bilgisi');
            $table->unsignedInteger('status_id')->default(0)->index()->comment('Durum kimliği');
            $table->ipAddress('ip_address')->nullable()->comment('Oluşturan IP adresi');
            $table->string('user_agent', 500)->nullable()->comment('Tarayıcı bilgisi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['email', 'created_at'], 'idx_feedback_email_date');
            $table->index('deleted_at', 'idx_feedback_soft_delete');
        });

        Schema::create(self::TABLE_TICKET, function (Blueprint $table) {
            $table->comment('Destek talepleri (Ticket) tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('ticket_id')->comment('Talep benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Talep kodu');
            $table->string('subject', 255)->comment('Talep konusu');
            $table->unsignedBigInteger('account_id')->index()->comment('Müşteri/Hesap kimliği');
            $table->unsignedBigInteger('assistant_id')->default(0)->comment('Atanan asistan/personel kimliği');
            $table->unsignedInteger('department_id')->default(0)->comment('Departman kimliği');
            $table->unsignedInteger('priority_id')->default(0)->comment('Öncelik kimliği');
            $table->unsignedBigInteger('relation_id')->default(0)->comment('İlişkili kayıt kimliği (Sipariş vb.)');
            $table->string('relation_type', 100)->nullable()->comment('İlişkili kaynak tipi (Order, Product vb.)');
            $table->unsignedInteger('status_id')->default(0)->index()->comment('Durum kimliği');
            $table->boolean('is_locked')->default(false)->comment('Kilitli mi?');
            $table->ipAddress('ip_address')->nullable()->comment('Oluşturan IP adresi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['account_id', 'status_id'], 'idx_ticket_account_status');
            $table->index(['relation_id', 'relation_type'], 'idx_ticket_relation');
            $table->index('deleted_at', 'idx_ticket_soft_delete');
        });

        Schema::create(self::TABLE_TICKET_MESSAGE, function (Blueprint $table) {
            $table->comment('Destek talebi mesajları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('ticket_message_id')->comment('Mesaj benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('ticket_id')->comment('Ticket kimliği');
            $table->unsignedBigInteger('account_id')->default(0)->comment('Mesajı yazan müşteri kimliği (0 ise personel)');
            $table->unsignedBigInteger('assistant_id')->default(0)->comment('Mesajı yazan personel kimliği (0 ise müşteri)');
            $table->longText('message')->comment('Mesaj içeriği');
            $table->text('attachments')->nullable()->comment('Ek dosyalar (JSON)');
            $table->boolean('is_read')->default(false)->comment('Okundu bilgisi');
            $table->boolean('is_private')->default(false)->comment('Özel not mu? (Sadece personel görür)');
            $table->ipAddress('ip_address')->nullable()->comment('IP adresi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['ticket_id', 'created_at'], 'idx_ticket_msg_thread');
            $table->index('deleted_at', 'idx_ticket_msg_soft_delete');
        });

        Schema::create(self::TABLE_FAQ, function (Blueprint $table) {
            $table->comment('Sıkça sorulan sorular tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('faq_id')->comment('SSS benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Referans kodu');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->boolean('is_active')->default(true)->index()->comment('Aktif mi?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_faq_soft_delete');
        });

        Schema::create(self::TABLE_FAQ_TRANSLATION, function (Blueprint $table) {
            $table->comment('SSS çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('faq_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('faq_id')->comment('SSS kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('question', 500)->comment('Soru');
            $table->longText('answer')->nullable()->comment('Cevap');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['faq_id', 'language_code'], 'idx_faq_lang_unique');
            $table->fullText(['question', 'answer'], 'idx_faq_fulltext');
            $table->index('deleted_at', 'idx_faq_trans_soft_delete');
        });

        Schema::create(self::TABLE_FAQ_GROUP, function (Blueprint $table) {
            $table->comment('SSS-Grup ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('faq_group_relation_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('faq_id')->comment('SSS kimliği');
            $table->unsignedBigInteger('faq_group_id')->comment('Grup kimliği');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['faq_id', 'faq_group_id'], 'idx_faq_group_unique');
            $table->index('deleted_at', 'idx_faq_group_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE, function (Blueprint $table) {
            $table->comment('Bilgi bankası makale tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_id')->comment('Makale benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Makale kodu');
            $table->string('image', 500)->nullable()->comment('Kapak görseli');
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->unsignedInteger('layout_id')->default(0)->comment('Düzen kimliği');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['viewed', 'status'], 'idx_article_popular');
            $table->index('deleted_at', 'idx_article_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Makale çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('article_id')->comment('Makale kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Makale başlığı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
            $table->text('summary')->nullable()->comment('Özet');
            $table->longText('description')->nullable()->comment('İçerik');
            $table->string('tag', 500)->nullable()->comment('Etiketler');
            $table->string('keyword', 500)->nullable()->comment('Anahtar kelimeler');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['article_id', 'language_code'], 'idx_article_lang_unique');
            $table->fullText(['name', 'description', 'summary'], 'idx_article_fulltext');
            $table->index('deleted_at', 'idx_article_trans_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE_CATEGORY, function (Blueprint $table) {
            $table->comment('Makale-Kategori ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_category_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('article_id')->comment('Makale kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['article_id', 'category_id'], 'idx_article_category_unique');
            $table->index('deleted_at', 'idx_article_cat_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE_IMAGE, function (Blueprint $table) {
            $table->comment('Makale görselleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('article_id')->comment('Makale kimliği');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['article_id', 'sort_order'], 'idx_article_image_sort');
            $table->index('deleted_at', 'idx_article_img_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE_VIDEO, function (Blueprint $table) {
            $table->comment('Makale videoları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_video_id')->comment('Video benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('article_id')->comment('Makale kimliği');
            $table->enum('source', ['youtube', 'vimeo', 'url', 'file', 'embed'])->default('youtube')->comment('Video kaynağı');
            $table->string('content', 1000)->comment('İçerik/URL');
            $table->string('name', 255)->nullable()->comment('Video adı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['article_id', 'sort_order'], 'idx_article_video_sort');
            $table->index('deleted_at', 'idx_article_vid_soft_delete');
        });

        Schema::create(self::TABLE_ARTICLE_RELATED, function (Blueprint $table) {
            $table->comment('İlişkili makaleler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('article_related_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('article_id')->comment('Makale kimliği');
            $table->unsignedBigInteger('related_id')->comment('İlişkili makale kimliği');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['article_id', 'related_id'], 'idx_article_related_unique');
            $table->index('deleted_at', 'idx_article_rel_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_ARTICLE_RELATED);
        Schema::dropIfExists(self::TABLE_ARTICLE_VIDEO);
        Schema::dropIfExists(self::TABLE_ARTICLE_IMAGE);
        Schema::dropIfExists(self::TABLE_ARTICLE_CATEGORY);
        Schema::dropIfExists(self::TABLE_ARTICLE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_ARTICLE);
        Schema::dropIfExists(self::TABLE_FAQ_GROUP);
        Schema::dropIfExists(self::TABLE_FAQ_TRANSLATION);
        Schema::dropIfExists(self::TABLE_FAQ);
        Schema::dropIfExists(self::TABLE_TICKET_MESSAGE);
        Schema::dropIfExists(self::TABLE_TICKET);
        Schema::dropIfExists(self::TABLE_FEEDBACK);
    }
};
