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

    private const TABLE_CASH_TYPE = 'def_acc_cash_type';

    private const TABLE_CASH_TYPE_TRANSLATION = 'def_acc_cash_type_translation';

    private const TABLE_TRANSACTION_FLOW = 'def_acc_transaction_flow';

    private const TABLE_TRANSACTION_FLOW_TRANSLATION = 'def_acc_transaction_flow_translation';

    private const TABLE_TRANSACTION_GROUP = 'def_acc_transaction_group';

    private const TABLE_TRANSACTION_GROUP_TRANSLATION = 'def_acc_transaction_group_translation';

    private const TABLE_TRANSACTION_TYPE = 'def_acc_transaction_type';

    private const TABLE_TRANSACTION_TYPE_TRANSLATION = 'def_acc_transaction_type_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_CASH_TYPE, function (Blueprint $table) {
            $table->comment('Kasa işlem türleri (Nakit, POS, Havale vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_id')->comment('Tür benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->string('color', 20)->nullable()->comment('Renk kodu');
            $table->boolean('is_system')->default(false)->comment('Sistem tanımlı mı?');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_cash_type_soft_delete');
        });

        Schema::create(self::TABLE_CASH_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kasa türü çeviri tablosu');
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

            $table->unique(['type_id', 'language_code'], 'idx_cash_type_lang_unique');
            $table->index('deleted_at', 'idx_cash_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_FLOW, function (Blueprint $table) {
            $table->comment('İşlem akış/yön tanımları (Giriş, Çıkış, Virman)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('flow_id')->comment('Akış benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->string('color', 20)->nullable()->comment('Renk kodu');
            $table->unsignedSmallInteger('priority')->default(0)->comment('Öncelik');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_flow_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_FLOW_TRANSLATION, function (Blueprint $table) {
            $table->comment('İşlem akış çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('flow_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('flow_id')->comment('Akış kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Akış adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['flow_id', 'language_code'], 'idx_flow_lang_unique');
            $table->index('deleted_at', 'idx_flow_trans_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_GROUP, function (Blueprint $table) {
            $table->comment('İşlem grupları (Satış, Satınalma, Masraf vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('group_id')->comment('Grup benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->string('color', 20)->nullable()->comment('Renk kodu');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_trans_grp_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_GROUP_TRANSLATION, function (Blueprint $table) {
            $table->comment('İşlem grubu çeviri tablosu');
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

            $table->unique(['group_id', 'language_code'], 'idx_trans_grp_lang_unique');
            $table->index('deleted_at', 'idx_trans_grp_trans_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_TYPE, function (Blueprint $table) {
            $table->comment('İşlem tipleri (Fatura, Tahsilat, Ödeme vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_id')->comment('Tip benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->unsignedBigInteger('flow_id')->index()->comment('Akış kimliği');
            $table->unsignedBigInteger('group_id')->index()->comment('Grup kimliği');
            $table->boolean('menu_view')->default(false)->comment('Menüde göster');
            $table->string('request_id', 100)->nullable()->comment('İstek tanımlayıcısı');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->string('color', 20)->nullable()->comment('Renk');
            $table->string('module', 100)->nullable()->comment('Bağlı olduğu modül');
            $table->string('route', 255)->nullable()->comment('Rota');
            $table->boolean('is_visible')->default(true)->comment('Görünürlük');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_trans_type_soft_delete');
        });

        Schema::create(self::TABLE_TRANSACTION_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('İşlem tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('type_id')->comment('Tip kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Tip adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['type_id', 'language_code'], 'idx_trans_type_lang_unique');
            $table->index('deleted_at', 'idx_trans_type_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_TRANSACTION_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TRANSACTION_TYPE);
        Schema::dropIfExists(self::TABLE_TRANSACTION_GROUP_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TRANSACTION_GROUP);
        Schema::dropIfExists(self::TABLE_TRANSACTION_FLOW_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TRANSACTION_FLOW);
        Schema::dropIfExists(self::TABLE_CASH_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CASH_TYPE);
    }
};
