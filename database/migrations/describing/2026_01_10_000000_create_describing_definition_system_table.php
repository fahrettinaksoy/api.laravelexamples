<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bu migration'ın kullanacağı veritabanı bağlantısının adı.
     * Bu özellik tanımlandığında, bu sınıf içindeki tüm Schema işlemleri
     * 'conn_desc' bağlantısı üzerinde otomatik olarak yürütülür.
     */
    protected $connection = 'conn_desc';

    // Sistem Bileşenleri Tablosu
    private const SYSTEM_COMPONENT_TABLE = 'dfntn_systm_component';

    /**
     * Migration'ı çalıştır. Gerekli tüm sistem bileşeni tanımlama tablolarını belirtilen 'conn_desc' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_systm_component tablosunu oluştur
        Schema::create(self::SYSTEM_COMPONENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Sistem bileşenlerini (modüller, operasyonlar, özellikler) tanımlar.');

            $table->bigIncrements('component_id')->comment('Bileşen için birincil anahtar');
            $table->string('parent_code', 100)->nullable()->comment('Üst bileşenin kodu (örn: "catalog" için "product")'); // 255 yerine 100
            $table->string('operation', 100)->nullable()->comment('Bileşenin temsil ettiği operasyon veya aksiyon (örn: "create", "read", "update", "delete")'); // 255 yerine 100
            $table->string('code', 100)->unique()->comment('Bileşen için benzersiz kod (örn: "product_list", "user_management")'); // 255 yerine 100
            $table->string('slug', 100)->nullable()->comment('Bileşene ait URL dostu kimlik veya alternatif kod'); // 255 yerine 100
            $table->text('education_video')->nullable()->comment('Bileşenle ilgili eğitim videosunun URL veya embed kodu');
            $table->unsignedBigInteger('recurring_type_id')->default(0)->nullable()->comment('Eğer bileşen bir abonelik tipiyle ilişkiliyse, ilgili tekrarlayan tip ID\'si'); // string yerine unsignedBigInteger
            $table->string('icon', 100)->nullable()->comment('Bileşenin ikon sınıfı veya URL'); // 255 yerine 100
            $table->string('controller', 255)->nullable()->comment('Bileşenin ilişkili olduğu Controller sınıfı');
            $table->text('method')->nullable()->comment('Bileşenin ilişkili olduğu Controller metodları (JSON array veya virgülle ayrılmış metin)'); // 255 yerine text
            $table->integer('sort_order')->default(0)->comment('Bileşenlerin listeleme sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('parent_code', 'idx_dfntn_systm_comp_parent_code');
            $table->index('operation', 'idx_dfntn_systm_comp_operation');
            $table->index('code', 'idx_dfntn_systm_comp_code'); // Zaten unique, ek indeks belirtildi
            $table->index('slug', 'idx_dfntn_systm_comp_slug');
            $table->index('recurring_type_id', 'idx_dfntn_systm_comp_rec_type_id');
            $table->index('sort_order', 'idx_dfntn_systm_comp_sort_order');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_desc' bağlantısı üzerinden siler.
     */
    public function down(): void
    {
        // Tabloyu sil
        Schema::dropIfExists(self::SYSTEM_COMPONENT_TABLE);
    }
};
