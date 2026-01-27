<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_GENDER = 'def_gen_gender';
    private const TABLE_GENDER_TRANSLATION = 'def_gen_gender_translation';
    private const TABLE_BANK = 'def_gen_bank';
    private const TABLE_CARGO = 'def_gen_cargo';

    public function up(): void
    {
        Schema::create(self::TABLE_GENDER, function (Blueprint $table) {
            $table->comment('Cinsiyet tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('gender_id')->comment('Cinsiyet benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz kod (male, female vb.)');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_gender_soft_delete');
        });

        Schema::create(self::TABLE_GENDER_TRANSLATION, function (Blueprint $table) {
            $table->comment('Cinsiyet çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('gender_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('gender_id')->comment('Cinsiyet kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 100)->comment('Cinsiyet adı');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['gender_id', 'language_code'], 'idx_gender_lang_unique');
            $table->index('deleted_at', 'idx_gender_trans_soft_delete');
        });

        Schema::create(self::TABLE_BANK, function (Blueprint $table) {
            $table->comment('Banka tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('bank_id')->comment('Banka benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Banka kodu');
            $table->string('type', 50)->nullable()->comment('Banka tipi');
            $table->string('name', 255)->comment('Banka adı');
            $table->string('eft_code', 50)->nullable()->comment('EFT kodu');
            $table->string('swift_code', 50)->nullable()->comment('SWIFT kodu');
            $table->string('telex_code', 50)->nullable()->comment('Telex kodu');
            $table->string('image', 500)->nullable()->comment('Logo görseli');
            $table->string('website', 255)->nullable()->comment('Web sitesi');
            $table->string('email', 255)->nullable()->comment('İletişim e-posta');
            $table->string('call_number', 50)->nullable()->comment('Çağrı merkezi');
            $table->string('telephone_number', 50)->nullable()->comment('Telefon');
            $table->string('fax_number', 50)->nullable()->comment('Faks');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_bank_soft_delete');
        });

        Schema::create(self::TABLE_CARGO, function (Blueprint $table) {
            $table->comment('Kargo firması tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('cargo_id')->comment('Kargo benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Kargo kodu');
            $table->string('name', 255)->comment('Kargo firması adı');
            $table->string('image', 500)->nullable()->comment('Logo görseli');
            $table->string('website', 255)->nullable()->comment('Web sitesi');
            $table->string('tracking_url', 500)->nullable()->comment('Kargo takip URL şablonu');
            $table->string('email', 255)->nullable()->comment('İletişim e-posta');
            $table->string('call_number', 50)->nullable()->comment('Çağrı merkezi');
            $table->string('telephone_number', 50)->nullable()->comment('Telefon');
            $table->string('fax_number', 50)->nullable()->comment('Faks');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_cargo_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_CARGO);
        Schema::dropIfExists(self::TABLE_BANK);
        Schema::dropIfExists(self::TABLE_GENDER_TRANSLATION);
        Schema::dropIfExists(self::TABLE_GENDER);
    }
};
