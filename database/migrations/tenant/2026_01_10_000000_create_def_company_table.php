<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('def_spprt_department', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kurumsal departmanları ve bölümleri tanımlar. Şirket içi yapı yönetimi için departman adları, açıklamaları ve durum bilgilerini saklar.');

            $table->bigIncrements('department_id')->comment('Departman için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 100)->unique()->comment('Departman adı');
            $table->boolean('status')->default(false)->comment('Departman durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_department_status');
            $table->index('created_by', 'idx_department_created_by');
            $table->index('updated_by', 'idx_department_updated_by');
        });
		
        Schema::create('def_spprt_department_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8mb4';
			$table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('department_translation_id');
			$table->integer('department_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cmp_branch', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şube ofisleri için konum detayları, iletişim bilgileri ve operasyon durumunu içeren kapsamlı bilgileri saklar. Organizasyonun tüm fiziksel iş lokasyonlarını yönetir.');

            $table->bigIncrements('branch_id')->comment('Şube için birincil anahtar');
            $table->string('code', 50)->unique()->comment('Benzersiz şube kodu');
            $table->string('type', 50)->comment('Şube türü (örn. perakende, kurumsal)');
            $table->string('name', 100)->comment('Şube adı');
            $table->string('company', 100)->comment('Şirket adı');
            $table->string('authorized', 100)->nullable()->comment('Yetkili kişi');
            $table->string('image')->nullable()->comment('Şube resmi için URL veya dosya yolu');
            $table->string('website')->nullable()->comment('Şube web sitesi URL\'si');
            $table->string('email', 100)->comment('Şube e-posta adresi');
            $table->string('phone_number', 20)->nullable()->comment('Şube telefon numarası');
            $table->string('fax_number', 20)->nullable()->comment('Şube faks numarası');
            $table->string('gsm_number', 20)->nullable()->comment('Şube mobil numarası');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülkeye referans');
            $table->unsignedBigInteger('city_id')->nullable()->comment('Şehre referans');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçeye referans');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->string('address_1', 255)->nullable()->comment('Birincil adres');
            $table->string('address_2', 255)->nullable()->comment('İkincil adres');
            $table->string('map_coordinate', 100)->nullable()->comment('Coğrafi koordinatlar (enlem,boylam)');
            $table->boolean('status')->default(false)->comment('Şube durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_branch_status');
            $table->index('country_id', 'idx_branch_country_id');
            $table->index('city_id', 'idx_branch_city_id');
            $table->index('district_id', 'idx_branch_district_id');
            $table->index('created_by', 'idx_branch_created_by');
            $table->index('updated_by', 'idx_branch_updated_by');
            $table->index('name', 'idx_branch_name');
            $table->index('email', 'idx_branch_email');
            $table->index('phone_number', 'idx_branch_phone_number');
            $table->index('type', 'idx_branch_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('def_cmp_department');
        Schema::dropIfExists('def_cmp_department_translation');
		
        Schema::dropIfExists('def_cmp_branch');
    }
};
