<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_LANGUAGE = 'def_loc_language';
    private const TABLE_CURRENCY = 'def_loc_currency';
    private const TABLE_COUNTRY = 'def_loc_country';
    private const TABLE_CITY = 'def_loc_city';
    private const TABLE_DISTRICT = 'def_loc_district';
    private const TABLE_GEO = 'def_loc_geo';
    private const TABLE_GEO_ZONE = 'def_loc_geo_zone';
    
    private const TABLE_LENGTH_CLASS = 'def_loc_length_class';
    private const TABLE_LENGTH_CLASS_TRANSLATION = 'def_loc_length_class_translation';
    
    private const TABLE_WEIGHT_CLASS = 'def_loc_weight_class';
    private const TABLE_WEIGHT_CLASS_TRANSLATION = 'def_loc_weight_class_translation';
    
    private const TABLE_UNIT = 'def_loc_unit';
    private const TABLE_UNIT_TRANSLATION = 'def_loc_unit_translation';
    
    private const TABLE_TAX_CLASS = 'def_loc_tax_class';
    private const TABLE_TAX_CLASS_TRANSLATION = 'def_loc_tax_class_translation';
    private const TABLE_TAX_CLASS_ACCOUNT_GROUP = 'def_loc_tax_class_account_group';
    
    private const TABLE_TAX_RATE = 'def_loc_tax_rate';
    private const TABLE_TAX_RATE_ACCOUNT_GROUP = 'def_loc_tax_rate_account_group';
    
    private const TABLE_ADDRESS_TYPE = 'def_loc_address_type';
    private const TABLE_ADDRESS_TYPE_TRANSLATION = 'def_loc_address_type_translation';
    
    private const TABLE_TAXPAYER_TYPE = 'def_loc_taxpayer_type';
    private const TABLE_TAXPAYER_TYPE_TRANSLATION = 'def_loc_taxpayer_type_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_LANGUAGE, function (Blueprint $table) {
            $table->comment('Sistem dilleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('language_id')->comment('Dil benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 10)->unique()->comment('ISO kodu (tr, en-us)');
            $table->string('name', 100)->comment('Dil adı');
            $table->string('flag', 255)->nullable()->comment('Bayrak görseli');
            $table->string('direction', 10)->default('ltr')->comment('Yön (ltr/rtl)');
            $table->string('directory', 50)->nullable()->comment('Dil dizini');
            $table->string('locale', 20)->nullable()->comment('Locale (tr_TR.UTF-8)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_lang_soft_delete');
        });

        Schema::create(self::TABLE_CURRENCY, function (Blueprint $table) {
            $table->comment('Para birimleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('currency_id')->comment('Para birimi benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 10)->unique()->comment('ISO kodu (TRY, USD)');
            $table->string('name', 100)->comment('Para birimi adı');
            $table->string('symbol_left', 10)->nullable()->comment('Sol sembol');
            $table->string('symbol_right', 10)->nullable()->comment('Sağ sembol');
            $table->integer('decimal_place')->default(2)->comment('Ondalık hane');
            $table->string('decimal_point', 5)->default('.')->comment('Ondalık ayracı');
            $table->string('thousand_point', 5)->default(',')->comment('Binlik ayracı');
            $table->decimal('value', 15, 8)->default(1)->comment('Kur değeri');
            $table->boolean('is_default')->default(false)->comment('Varsayılan para birimi mi?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_curr_soft_delete');
        });

        Schema::create(self::TABLE_COUNTRY, function (Blueprint $table) {
            $table->comment('Ülke tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('country_id')->comment('Ülke benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 10)->unique()->comment('ISO alpha-2 kodu (TR)');
            $table->string('iso_alpha_3', 10)->nullable()->comment('ISO alpha-3 kodu (TUR)');
            $table->string('name', 255)->comment('Ülke adı');
            $table->string('phone_code', 10)->nullable()->comment('Telefon kodu (+90)');
            $table->string('address_format', 500)->nullable()->comment('Adres format şablonu');
            $table->boolean('postcode_required')->default(false)->comment('Posta kodu zorunlu mu?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_country_soft_delete');
        });

        Schema::create(self::TABLE_CITY, function (Blueprint $table) {
            $table->comment('Şehir tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('city_id')->comment('Şehir benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('country_id')->index()->comment('Ülke kimliği');
            $table->string('name', 255)->comment('Şehir adı');
            $table->string('code', 50)->nullable()->comment('Plaka/Bölge kodu');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_city_soft_delete');
        });

        Schema::create(self::TABLE_DISTRICT, function (Blueprint $table) {
            $table->comment('İlçe tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('district_id')->comment('İlçe benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('city_id')->index()->comment('Şehir kimliği');
            $table->string('name', 255)->comment('İlçe adı');
            $table->string('code', 50)->nullable()->comment('İlçe kodu');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_district_soft_delete');
        });

        Schema::create(self::TABLE_GEO, function (Blueprint $table) {
            $table->comment('Coğrafi bölge tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('geo_id')->comment('Bölge benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('name', 255)->comment('Bölge adı');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_geo_soft_delete');
        });

        Schema::create(self::TABLE_GEO_ZONE, function (Blueprint $table) {
            $table->comment('Coğrafi bölge kapsamları (Zone)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('geo_zone_id')->comment('Zone benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('geo_id')->index()->comment('Bölge kimliği');
            $table->unsignedBigInteger('country_id')->default(0)->index()->comment('Ülke kimliği');
            $table->unsignedBigInteger('city_id')->default(0)->index()->comment('Şehir kimliği');
            $table->unsignedBigInteger('district_id')->default(0)->comment('İlçe kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_geo_zone_soft_delete');
        });

        Schema::create(self::TABLE_LENGTH_CLASS, function (Blueprint $table) {
            $table->comment('Uzunluk birimleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('length_class_id')->comment('Birim benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->decimal('value', 15, 8)->default(1)->comment('Değer çarpanı');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_len_class_soft_delete');
        });

        Schema::create(self::TABLE_LENGTH_CLASS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Uzunluk birimi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('length_class_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('length_class_id')->comment('Birim kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 100)->comment('Birim adı (cm, m)');
            $table->string('unit', 20)->comment('Birim sembolü');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['length_class_id', 'language_code'], 'idx_len_class_lang_unique');
            $table->index('deleted_at', 'idx_len_class_trans_soft_delete');
        });

        Schema::create(self::TABLE_WEIGHT_CLASS, function (Blueprint $table) {
            $table->comment('Ağırlık birimleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('weight_class_id')->comment('Birim benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->decimal('value', 15, 8)->default(1)->comment('Değer çarpanı');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_wgt_class_soft_delete');
        });

        Schema::create(self::TABLE_WEIGHT_CLASS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ağırlık birimi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('weight_class_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('weight_class_id')->comment('Birim kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 100)->comment('Birim adı (kg, gr)');
            $table->string('unit', 20)->comment('Birim sembolü');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['weight_class_id', 'language_code'], 'idx_wgt_class_lang_unique');
            $table->index('deleted_at', 'idx_wgt_class_trans_soft_delete');
        });

        Schema::create(self::TABLE_UNIT, function (Blueprint $table) {
            $table->comment('Birimler tablosu (Adet, Koli vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('unit_id')->comment('Birim benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->decimal('value', 15, 8)->default(1)->comment('Değer');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_unit_soft_delete');
        });

        Schema::create(self::TABLE_UNIT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Birim çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('unit_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('unit_id')->comment('Birim kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 100)->comment('Birim adı');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['unit_id', 'language_code'], 'idx_unit_lang_unique');
            $table->index('deleted_at', 'idx_unit_trans_soft_delete');
        });

        Schema::create(self::TABLE_TAX_CLASS, function (Blueprint $table) {
            $table->comment('Vergi sınıfları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('tax_class_id')->comment('Vergi sınıfı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('name', 255)->comment('Vergi sınıfı adı');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_tax_class_soft_delete');
        });

        Schema::create(self::TABLE_TAX_CLASS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Vergi sınıfı çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('tax_class_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('tax_class_id')->comment('Vergi sınıfı kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Ad');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['tax_class_id', 'language_code'], 'idx_tax_class_lang_unique');
            $table->index('deleted_at', 'idx_tax_class_trans_soft_delete');
        });

        Schema::create(self::TABLE_TAX_RATE, function (Blueprint $table) {
            $table->comment('Vergi oranları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('tax_rate_id')->comment('Vergi oranı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('geo_zone_id')->default(0)->index()->comment('Bölge kapsamı (0=Tümü)');
            $table->string('name', 255)->comment('Oran adı (KDV %18)');
            $table->decimal('rate', 15, 4)->default(0)->comment('Vergi oranı');
            $table->char('type', 1)->default('P')->comment('Tip: P=Yüzde, F=Sabit');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_tax_rate_soft_delete');
        });

        Schema::create(self::TABLE_TAX_CLASS_ACCOUNT_GROUP, function (Blueprint $table) {
            $table->comment('Vergi sınıfı - Müşteri grubu ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('tax_class_account_group_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('tax_class_id')->comment('Vergi sınıfı');
            $table->unsignedBigInteger('account_group_id')->comment('Müşteri grubu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->unique(['tax_class_id', 'account_group_id'], 'idx_tc_ag_unique');
        });

        Schema::create(self::TABLE_TAX_RATE_ACCOUNT_GROUP, function (Blueprint $table) {
            $table->comment('Vergi oranı - Müşteri grubu ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('tax_rate_account_group_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('tax_rate_id')->comment('Vergi oranı');
            $table->unsignedBigInteger('account_group_id')->comment('Müşteri grubu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->unique(['tax_rate_id', 'account_group_id'], 'idx_tr_ag_unique');
        });

        Schema::create(self::TABLE_ADDRESS_TYPE, function (Blueprint $table) {
            $table->comment('Adres tipleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('address_type_id')->comment('Adres tipi benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Tip kodu');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_addr_type_soft_delete');
        });

        Schema::create(self::TABLE_ADDRESS_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Adres tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('address_type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('address_type_id')->comment('Adres tipi kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Ad');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['address_type_id', 'language_code'], 'idx_addr_type_lang_unique');
            $table->index('deleted_at', 'idx_addr_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_TAXPAYER_TYPE, function (Blueprint $table) {
            $table->comment('Vergi mükellefiyet tipleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('taxpayer_type_id')->comment('Mükellef tipi benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Tip kodu');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index('deleted_at', 'idx_taxpayer_type_soft_delete');
        });

        Schema::create(self::TABLE_TAXPAYER_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Vergi mükellefiyet tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('taxpayer_type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('taxpayer_type_id')->comment('Mükellef tipi kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Ad');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['taxpayer_type_id', 'language_code'], 'idx_taxpayer_type_lang_unique');
            $table->index('deleted_at', 'idx_taxpayer_type_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_TAXPAYER_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TAXPAYER_TYPE);
        Schema::dropIfExists(self::TABLE_ADDRESS_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_ADDRESS_TYPE);
        Schema::dropIfExists(self::TABLE_TAX_RATE_ACCOUNT_GROUP);
        Schema::dropIfExists(self::TABLE_TAX_CLASS_ACCOUNT_GROUP);
        Schema::dropIfExists(self::TABLE_TAX_RATE);
        Schema::dropIfExists(self::TABLE_TAX_CLASS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_TAX_CLASS);
        Schema::dropIfExists(self::TABLE_UNIT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_UNIT);
        Schema::dropIfExists(self::TABLE_WEIGHT_CLASS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_WEIGHT_CLASS);
        Schema::dropIfExists(self::TABLE_LENGTH_CLASS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_LENGTH_CLASS);
        Schema::dropIfExists(self::TABLE_GEO_ZONE);
        Schema::dropIfExists(self::TABLE_GEO);
        Schema::dropIfExists(self::TABLE_DISTRICT);
        Schema::dropIfExists(self::TABLE_CITY);
        Schema::dropIfExists(self::TABLE_COUNTRY);
        Schema::dropIfExists(self::TABLE_CURRENCY);
        Schema::dropIfExists(self::TABLE_LANGUAGE);
    }
};
