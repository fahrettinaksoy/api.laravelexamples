<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';
	
    public function up()
    {
        Schema::create('sys_application_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulama ve modül konfigürasyon değerlerini depolar');

            $table->bigIncrements('application_value_id')->comment('Uygulama değeri birincil anahtarı');
            $table->string('code', 50)->nullable()->comment('Uygulama kod');
            $table->string('group_code', 100)->comment('Uygulama grubu');
            $table->string('type_code', 100)->comment('Uygulama türü');
            $table->text('setting')->nullable()->comment('Konfigürasyon değeri');
            $table->unsignedBigInteger('sort_order')->default(0)->comment('Sıralama için öncelik değeri');
            $table->boolean('status')->default(false)->comment('Uygulama aktif mi? (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID’si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı ID’si');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncellenme zamanı');

            $table->index('created_by', 'idx_application_created_by');
            $table->index('updated_by', 'idx_application_updated_by');
        });
		
        Schema::create('sys_setting_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistem genelindeki ayarları depolar');

            $table->bigIncrements('setting_value_id')->comment('Ayar birincil anahtarı');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('option', 255)->comment('Ayarın adı');
            $table->string('slug', 255)->unique()->comment('Ayar için benzersiz slug');
            $table->text('value')->nullable()->comment('Ayar değeri');
            $table->string('description', 255)->nullable()->comment('Ayar açıklaması');
            $table->boolean('serialized')->default(false)->comment('Değer serileştirilmiş mi? (false: Hayır, true: Evet)');
            $table->boolean('status')->default(false)->comment('Ayar aktif mi? (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID’si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı ID’si');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncellenme zamanı');

            $table->index('status', 'idx_setting_status');
            $table->index('created_by', 'idx_setting_created_by');
            $table->index('updated_by', 'idx_setting_updated_by');
            $table->index('serialized', 'idx_setting_serialized');
        });
		
        Schema::create('sys_company_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şirkete özgü tanımlamaları ve konfigürasyonları depolar');

            $table->bigIncrements('company_value_id')->comment('Şirket tanımı birincil anahtarı');
            $table->string('option', 255)->comment('Tanımın adı');
            $table->string('slug', 255)->unique()->comment('Benzersiz slug');
            $table->text('value')->nullable()->comment('Tanım değeri');
            $table->boolean('view')->default(false)->comment('Ayar gözükür mi? (false: Hayır, true: Evet)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID’si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı ID’si');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncellenme zamanı');

            $table->index('created_by', 'idx_company_created_by');
            $table->index('updated_by', 'idx_company_updated_by');
        });
		
        Schema::create('sys_method', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('method_id');
			$table->string('method_group', 255);
			$table->string('method_type', 255);
			$table->text('setting');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('sys_plugin', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('plugin_id');
			$table->string('plugin_group', 255);
			$table->string('plugin_type', 255);
			$table->text('setting');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('sys_widget', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('widget_id');
			$table->string('widget_type', 255);
			$table->string('name', 255);
			$table->string('css', 255);
			$table->string('html', 255);
			$table->text('setting');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('sys_variable_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Global anahtar-değer çiftlerini depolar');

            $table->bigIncrements('variable_value_id')->comment('Değişken birincil anahtarı');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('variable_key', 255)->unique()->comment('Değişken anahtarı');
            $table->text('variable_value')->nullable()->comment('Değişken değeri');
            $table->string('scope', 100)->nullable()->comment('Kapsam (ör. global, modül)');
            $table->boolean('status')->default(false)->comment('Aktif mi? (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID’si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı ID’si');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncellenme zamanı');

            $table->index('status', 'idx_variable_status');
            $table->index('scope', 'idx_variable_scope');
            $table->index('created_by', 'idx_variable_created_by');
            $table->index('updated_by', 'idx_variable_updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_application_value');
		
        Schema::dropIfExists('sys_setting_value');
        Schema::dropIfExists('sys_company_value');
		
        Schema::dropIfExists('sys_method');
        Schema::dropIfExists('sys_plugin');
        Schema::dropIfExists('sys_widget');
        Schema::dropIfExists('sys_variable');
    }
};
