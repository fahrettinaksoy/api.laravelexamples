<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const ACCOUNT_TYPE_TABLE = 'dfntn_gnrl_account_type';

    private const ACCOUNT_TYPE_TRANSLATION_TABLE = 'dfntn_gnrl_account_type_translation';

    private const ACCOUNT_SECTOR_TABLE = 'dfntn_gnrl_account_sector';

    private const ACCOUNT_SECTOR_TRANSLATION_TABLE = 'dfntn_gnrl_account_sector_translation';

    public function up(): void
    {
        Schema::create(self::ACCOUNT_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hesap tiplerini (örn: Bireysel Hesap, Kurumsal Hesap) tanımlar.');

            $table->bigIncrements('type_id')->comment('Hesap tipi için birincil anahtar');
            $table->tinyInteger('type')->default(0)->comment('Hesap tipi sayısal değeri (örn: 0: Bireysel, 1: Kurumsal). Bu alan daha çok iç sınıflandırma için kullanılır.');
            $table->boolean('status')->default(false)->comment('Hesap tipinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type', 'idx_dfntn_gnrl_acc_type_type');
            $table->index('status', 'idx_dfntn_gnrl_acc_type_status');
        });

        Schema::create(self::ACCOUNT_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hesap tiplerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('type_translation_id')->comment('Hesap tipi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('type_id')->comment('İlgili hesap tipi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)');
            $table->string('name', 255)->comment('Hesap tipinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Hesap tipinin çevrilmiş detaylı açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type_id', 'idx_dfntn_gnrl_acc_type_trans_type_id');
            $table->index('language_code', 'idx_dfntn_gnrl_acc_type_trans_lang_code');
            $table->unique(['type_id', 'language_code'], 'idx_dfntn_gnrl_acc_type_trans_unique_lang');
        });

        Schema::create(self::ACCOUNT_SECTOR_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hesapların ait olabileceği sektörleri (örn: Perakende, E-ticaret, Hizmet) tanımlar.');

            $table->bigIncrements('sector_id')->comment('Hesap sektörü için birincil anahtar');
            $table->integer('sort_order')->default(0)->comment('Sektörlerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Sektörün durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('sort_order', 'idx_dfntn_gnrl_acc_sect_sort_order');
            $table->index('status', 'idx_dfntn_gnrl_acc_sect_status');
        });

        Schema::create(self::ACCOUNT_SECTOR_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hesap sektörlerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('sector_translation_id')->comment('Hesap sektörü çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('sector_id')->comment('İlgili hesap sektörü ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Sektörün çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Sektörün çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Sektörün çevrilmiş detaylı açıklaması');
            $table->string('tag', 255)->nullable()->comment('Sektörün çevrilmiş etiketleri (virgülle ayrılmış)');
            $table->string('keyword', 255)->nullable()->comment('Sektörün SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('sector_id', 'idx_dfntn_gnrl_acc_sect_trans_sect_id');
            $table->index('language_code', 'idx_dfntn_gnrl_acc_sect_trans_lang_code');
            $table->unique(['sector_id', 'language_code'], 'idx_dfntn_gnrl_acc_sect_trans_unique_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::ACCOUNT_SECTOR_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_SECTOR_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ACCOUNT_TYPE_TABLE);
    }
};
