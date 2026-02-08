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

    private const TABLE_ACCOUNT_GROUP = 'def_mem_account_group';

    private const TABLE_ACCOUNT_GROUP_TRANSLATION = 'def_mem_account_group_translation';

    private const TABLE_ACCOUNT_TYPE = 'def_mem_account_type';

    private const TABLE_ACCOUNT_TYPE_TRANSLATION = 'def_mem_account_type_translation';

    private const TABLE_AUTHORIZED_GROUP = 'def_mem_account_authorized_group';

    private const TABLE_AUTHORIZED_GROUP_TRANSLATION = 'def_mem_account_authorized_group_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_ACCOUNT_GROUP, function (Blueprint $table) {
            $table->comment('Müşteri grupları (Bayi, Son kullanıcı vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('group_id')->comment('Grup benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->boolean('approval')->default(false)->comment('Yeni müşteriler için onay gerekir mi?');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_acc_group_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_GROUP_TRANSLATION, function (Blueprint $table) {
            $table->comment('Müşteri grubu çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('group_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('group_id')->comment('Grup kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Grup adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['group_id', 'language_code'], 'idx_acc_group_lang_unique');
            $table->index('deleted_at', 'idx_acc_group_trans_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_TYPE, function (Blueprint $table) {
            $table->comment('Müşteri tipleri (Bireysel, Kurumsal vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_id')->comment('Tür benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_acc_type_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Müşteri tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('type_id')->comment('Tür kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Tür adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['type_id', 'language_code'], 'idx_acc_type_lang_unique');
            $table->index('deleted_at', 'idx_acc_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_AUTHORIZED_GROUP, function (Blueprint $table) {
            $table->comment('Kurumsal hesap yetkili grupları/rolleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('authorized_group_id')->comment('Yetkili grup benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->text('permissions')->nullable()->comment('İzinler (JSON)');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_auth_group_soft_delete');
        });

        Schema::create(self::TABLE_AUTHORIZED_GROUP_TRANSLATION, function (Blueprint $table) {
            $table->comment('Yetkili grup çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('authorized_group_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('authorized_group_id')->comment('Yetkili grup kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Grup adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['authorized_group_id', 'language_code'], 'idx_auth_group_lang_unique');
            $table->index('deleted_at', 'idx_auth_group_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_AUTHORIZED_GROUP_TRANSLATION);
        Schema::dropIfExists(self::TABLE_AUTHORIZED_GROUP);
        Schema::dropIfExists(self::TABLE_ACCOUNT_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_ACCOUNT_TYPE);
        Schema::dropIfExists(self::TABLE_ACCOUNT_GROUP_TRANSLATION);
        Schema::dropIfExists(self::TABLE_ACCOUNT_GROUP);
    }
};
