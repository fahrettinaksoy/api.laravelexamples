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

    private const TABLE_DEPARTMENT = 'def_cmp_department';

    private const TABLE_DEPARTMENT_TRANSLATION = 'def_cmp_department_translation';

    private const TABLE_BRANCH = 'def_cmp_branch';

    public function up(): void
    {
        Schema::create(self::TABLE_DEPARTMENT, function (Blueprint $table) {
            $table->comment('Kurumsal departman tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('department_id')->comment('Departman benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 100)->comment('Departman adı');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_dept_soft_delete');
        });

        Schema::create(self::TABLE_DEPARTMENT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Departman çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('department_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('department_id')->comment('Departman kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Departman adı');
            $table->text('description')->nullable()->comment('Açıklama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['department_id', 'language_code'], 'idx_dept_lang_unique');
            $table->index('deleted_at', 'idx_dept_trans_soft_delete');
        });

        Schema::create(self::TABLE_BRANCH, function (Blueprint $table) {
            $table->comment('Şube ve lokasyon tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('branch_id')->comment('Şube benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->comment('Benzersiz şube kodu');
            $table->string('type', 50)->comment('Şube tipi (merkez, şube, depo vb.)');
            $table->string('name', 100)->comment('Şube adı');
            $table->string('company', 100)->nullable()->comment('Resmi şirket unvanı');
            $table->string('authorized_person', 100)->nullable()->comment('Yetkili kişi');
            $table->string('image', 500)->nullable()->comment('Görsel URL');
            $table->string('website', 255)->nullable()->comment('Web sitesi');
            $table->string('email', 100)->nullable()->comment('E-posta');
            $table->string('phone', 20)->nullable()->comment('Telefon');
            $table->string('fax', 20)->nullable()->comment('Faks');
            $table->string('gsm', 20)->nullable()->comment('GSM');

            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği');
            $table->unsignedBigInteger('city_id')->nullable()->comment('Şehir kimliği');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->string('address_1', 255)->nullable()->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            $table->string('map_coordinate', 100)->nullable()->comment('Harita koordinatı');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('country_id', 'idx_branch_country');
            $table->index('city_id', 'idx_branch_city');
            $table->index('type', 'idx_branch_type');
            $table->index('deleted_at', 'idx_branch_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_BRANCH);
        Schema::dropIfExists(self::TABLE_DEPARTMENT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_DEPARTMENT);
    }
};
