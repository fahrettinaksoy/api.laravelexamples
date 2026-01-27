<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_PRIORITY = 'def_spprt_priority';
    private const TABLE_PRIORITY_TRANSLATION = 'def_spprt_priority_translation';
    private const TABLE_RELATION = 'def_spprt_relation';
    private const TABLE_RELATION_TRANSLATION = 'def_spprt_relation_translation';
    private const TABLE_TICKET_STATUS = 'def_spprt_ticket_status';
    private const TABLE_TICKET_STATUS_TRANSLATION = 'def_spprt_ticket_status_translation';
    private const TABLE_FEEDBACK_STATUS = 'def_spprt_feedback_status';
    private const TABLE_FEEDBACK_STATUS_TRANSLATION = 'def_spprt_feedback_status_translation';
    private const TABLE_FAQ_GROUP = 'def_spprt_faq_group';
    private const TABLE_FAQ_GROUP_TRANSLATION = 'def_spprt_faq_group_translation';
    private const TABLE_KNOWLEDGE_CATEGORY = 'def_spprt_knowledge_category';
    private const TABLE_KNOWLEDGE_CATEGORY_TRANSLATION = 'def_spprt_knowledge_category_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_PRIORITY, function (Blueprint $table) {
            $table->comment('Destek talebi öncelik seviyeleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('priority_id')->comment('Öncelik benzersiz kimliği');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('color', 20)->nullable()->comment('Renk kodu');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_priority_soft_delete');
        });

        Schema::create(self::TABLE_PRIORITY_TRANSLATION, function (Blueprint $table) {
            $table->comment('Öncelik çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('priority_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('priority_id')->comment('Öncelik kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Öncelik adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['priority_id', 'language_code'], 'idx_priority_lang_unique');
            $table->index('deleted_at', 'idx_priority_trans_soft_delete');
        });

        Schema::create(self::TABLE_RELATION, function (Blueprint $table) {
            $table->comment('Destek talebi ilişkili konular/departmanlar');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('relation_id')->comment('Konu benzersiz kimliği');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('path', 255)->nullable()->comment('Hiyerarşik yol');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_relation_soft_delete');
        });

        Schema::create(self::TABLE_RELATION_TRANSLATION, function (Blueprint $table) {
            $table->comment('İlişki konu çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('relation_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('relation_id')->comment('Konu kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Konu adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['relation_id', 'language_code'], 'idx_relation_lang_unique');
            $table->index('deleted_at', 'idx_relation_trans_soft_delete');
        });

        Schema::create(self::TABLE_TICKET_STATUS, function (Blueprint $table) {
            $table->comment('Destek talebi durum tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('ticket_status_id')->comment('Durum benzersiz kimliği');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('color', 20)->nullable()->comment('Durum rengi');
            $table->boolean('is_closed')->default(false)->comment('Kapalı durum mu?');
            $table->boolean('status')->default(true)->index()->comment('Aktiflik durumu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_ticket_status_soft_delete');
        });

        Schema::create(self::TABLE_TICKET_STATUS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Talep durumu çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('ticket_status_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('ticket_status_id')->comment('Durum kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Durum adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['ticket_status_id', 'language_code'], 'idx_ticket_status_lang_unique');
            $table->index('deleted_at', 'idx_ticket_status_trans_soft_delete');
        });

        Schema::create(self::TABLE_FEEDBACK_STATUS, function (Blueprint $table) {
            $table->comment('Geri bildirim durum tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('feedback_status_id')->comment('Durum benzersiz kimliği');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('color', 20)->nullable()->comment('Durum rengi');
            $table->boolean('status')->default(true)->index()->comment('Aktiflik durumu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_feedback_status_soft_delete');
        });

        Schema::create(self::TABLE_FEEDBACK_STATUS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Geri bildirim durumu çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('feedback_status_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('feedback_status_id')->comment('Durum kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Durum adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['feedback_status_id', 'language_code'], 'idx_feedback_status_lang_unique');
            $table->index('deleted_at', 'idx_feedback_status_trans_soft_delete');
        });

        Schema::create(self::TABLE_FAQ_GROUP, function (Blueprint $table) {
            $table->comment('SSS Grupları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('faq_group_id')->comment('Grup benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_faq_group_soft_delete');
        });

        Schema::create(self::TABLE_FAQ_GROUP_TRANSLATION, function (Blueprint $table) {
            $table->comment('SSS Grup çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('faq_group_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('faq_group_id')->comment('Grup kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Grup adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['faq_group_id', 'language_code'], 'idx_faq_group_lang_unique');
            $table->index('deleted_at', 'idx_faq_group_trans_soft_delete');
        });

        Schema::create(self::TABLE_KNOWLEDGE_CATEGORY, function (Blueprint $table) {
            $table->comment('Bilgi bankası kategorileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_id')->comment('Kategori benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('Üst kategori kimliği');
            $table->string('image', 500)->nullable()->comment('Kategori görseli');
            $table->unsignedInteger('layout_id')->default(0)->comment('Düzen kimliği');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_kb_category_soft_delete');
        });

        Schema::create(self::TABLE_KNOWLEDGE_CATEGORY_TRANSLATION, function (Blueprint $table) {
            $table->comment('Bilgi bankası kategori çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Kategori adı');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['category_id', 'language_code'], 'idx_kb_category_lang_unique');
            $table->index('deleted_at', 'idx_kb_category_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_KNOWLEDGE_CATEGORY_TRANSLATION);
        Schema::dropIfExists(self::TABLE_KNOWLEDGE_CATEGORY);
        Schema::dropIfExists(self::TABLE_FAQ_GROUP_TRANSLATION);
        Schema::dropIfExists(self::TABLE_FAQ_GROUP);
        Schema::dropIfExists(self::TABLE_FEEDBACK_STATUS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_FEEDBACK_STATUS);
        Schema::dropIfExists(self::TABLE_TICKET_STATUS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TICKET_STATUS);
        Schema::dropIfExists(self::TABLE_RELATION_TRANSLATION);
        Schema::dropIfExists(self::TABLE_RELATION);
        Schema::dropIfExists(self::TABLE_PRIORITY_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRIORITY);
    }
};
