<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Response Mesajları
    |--------------------------------------------------------------------------
    */

    'success' => 'İşlem başarılı',
    'created' => 'Kayıt başarıyla oluşturuldu',
    'updated' => 'Kayıt başarıyla güncellendi',
    'deleted' => 'Kayıt başarıyla silindi',
    'not_found' => 'Kayıt bulunamadı',
    'unauthorized' => 'Yetkisiz erişim',
    'forbidden' => 'Bu işlem için yetkiniz yok',
    'too_many_requests' => 'Çok fazla istek gönderildi, lütfen bekleyin',
    'internal_error' => 'Sunucu hatası oluştu',

    /*
    |--------------------------------------------------------------------------
    | Validation Mesajları
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'error' => 'Doğrulama hatası',
        'fields_array' => 'Alanlar bir dizi olmalıdır',
        'include_string' => 'Dahil edilecek veriler metin olmalıdır',
        'sort_string' => 'Sıralama bilgisi metin olmalıdır',
        'limit_integer' => 'Limit bir tamsayı olmalıdır',
        'limit_min' => 'Limit en az 1 olmalıdır',
        'filter_array' => 'Filtreler bir dizi olmalıdır',
        'ids_must_be_array' => 'ID listesi bir dizi olmalıdır',
        'ids_min_one' => 'En az bir ID belirtilmelidir',
        'id_required' => 'Her ID değeri zorunludur',
        'id_integer' => 'ID değeri tam sayı olmalıdır',
        'id_min_one' => 'ID değeri en az 1 olmalıdır',
        'bulk_delete_criteria_required' => 'Toplu silme için en az bir kriter gereklidir',
        'field_update' => [
            'field_required' => 'Alan adı zorunludur',
            'field_string' => 'Alan adı metin olmalıdır',
            'field_not_updatable' => "':input' alanı güncellenemez",
            'value_present' => 'Değer alanı gönderilmelidir',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Resolution Mesajları
    |--------------------------------------------------------------------------
    */

    'route' => [
        'invalid_path' => 'Geçersiz path',
        'invalid_segment' => 'Geçersiz path segment',
        'invalid_pivot' => 'Geçersiz pivot route yapısı',
        'resource_not_found' => 'Kaynak bulunamadı',
        'model_not_found_debug' => 'modeli bulunamadı veya geçerli değil',
        'relation_not_found_debug' => 'ilişkisi bulunamadı',
    ],

    /*
    |--------------------------------------------------------------------------
    | Controller Mesajları
    |--------------------------------------------------------------------------
    */

    'controller' => [
        'invalid_request_keys' => 'Geçersiz request action key\'leri: :keys. Geçerli key\'ler: :valid',
        'invalid_dto_keys' => 'Geçersiz DTO action key\'leri: :keys. Geçerli key\'ler: :valid',
        'service_not_initialized' => 'Service başlatılmadı. Constructor\'dan inject edin veya RepositoryServiceProvider\'ı yapılandırın.',
        'request_not_defined' => ':action action\'ı için request sınıfı tanımlanmadı',
        'request_must_extend_base' => ':class sınıfı BaseRequest\'ten extend etmelidir',
        'model_not_resolved' => 'Model sınıfı çözümlenemedi. ValidateModule middleware\'inin aktif olduğundan emin olun.',
    ],

    /*
    |--------------------------------------------------------------------------
    | SmartQuery Mesajları
    |--------------------------------------------------------------------------
    */

    'smartquery' => [
        'filter_not_allowed' => '\':filter\' filtresi izin verilmiyor. İzin verilen filtreler: :allowed',
        'filters_not_allowed' => '\':filters\' filtreleri izin verilmiyor. İzin verilen filtreler: :allowed',
        'include_not_allowed' => '\':include\' include izin verilmiyor. İzin verilen include\'lar: :allowed',
        'includes_not_allowed' => '\':includes\' include\'ları izin verilmiyor. İzin verilen include\'lar: :allowed',
        'sort_not_allowed' => '\':sort\' sıralaması izin verilmiyor. İzin verilen sıralamalar: :allowed',
        'sorts_not_allowed' => '\':sorts\' sıralamaları izin verilmiyor. İzin verilen sıralamalar: :allowed',
        'field_not_allowed' => '\':field\' alanı izin verilmiyor. İzin verilen alanlar: :allowed',
        'fields_not_allowed' => '\':fields\' alanları izin verilmiyor. İzin verilen alanlar: :allowed',
        'invalid_filter_type' => 'Filtre string veya AllowedFilter instance olmalıdır. Gelen tip: :type',
        'invalid_include_type' => 'Include string veya AllowedInclude instance olmalıdır. Gelen tip: :type',
        'invalid_dynamic_operator' => 'Dinamik filtre değeri geçerli bir operatör prefix\'i içermelidir (=, !=, >, <, >=, <=). Gelen değer: :value',
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Mesajları
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Product Validation Mesajları
    |--------------------------------------------------------------------------
    */

    'product' => [
        'name_required' => 'Ürün adı zorunludur',
        'name_string' => 'Ürün adı metin olmalıdır',
        'name_max' => 'Ürün adı en fazla 255 karakter olabilir',
        'slug_required' => 'Slug zorunludur',
        'slug_string' => 'Slug metin olmalıdır',
        'slug_max' => 'Slug en fazla 255 karakter olabilir',
        'slug_unique' => 'Bu slug zaten kullanılıyor',
        'sku_required' => 'SKU zorunludur',
        'sku_string' => 'SKU metin olmalıdır',
        'sku_max' => 'SKU en fazla 100 karakter olabilir',
        'sku_unique' => 'Bu SKU zaten kullanılıyor',
        'short_description_max' => 'Kısa açıklama en fazla 500 karakter olabilir',
        'price_required' => 'Fiyat zorunludur',
        'price_numeric' => 'Fiyat sayısal bir değer olmalıdır',
        'price_min' => 'Fiyat 0 veya daha büyük olmalıdır',
        'sale_price_numeric' => 'İndirimli fiyat sayısal bir değer olmalıdır',
        'sale_price_min' => 'İndirimli fiyat 0 veya daha büyük olmalıdır',
        'sale_price_lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır',
        'cost_numeric' => 'Maliyet sayısal bir değer olmalıdır',
        'cost_min' => 'Maliyet 0 veya daha büyük olmalıdır',
        'stock_required' => 'Stok miktarı zorunludur',
        'stock_integer' => 'Stok miktarı tam sayı olmalıdır',
        'stock_min' => 'Stok miktarı 0 veya daha büyük olmalıdır',
        'category_id_integer' => 'Kategori ID tam sayı olmalıdır',
        'category_id_exists' => 'Seçilen kategori bulunamadı',
        'brand_id_integer' => 'Marka ID tam sayı olmalıdır',
        'brand_id_exists' => 'Seçilen marka bulunamadı',
        'is_active_boolean' => 'Aktiflik durumu doğru/yanlış olmalıdır',
        'is_featured_boolean' => 'Öne çıkarma durumu doğru/yanlış olmalıdır',
        'meta_title_max' => 'Meta başlık en fazla 255 karakter olabilir',
        'meta_description_max' => 'Meta açıklama en fazla 500 karakter olabilir',
        'meta_keywords_max' => 'Meta anahtar kelimeler en fazla 255 karakter olabilir',
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Mesajları
    |--------------------------------------------------------------------------
    */

    'health' => [
        'healthy' => 'Tüm servisler çalışıyor',
        'unhealthy' => 'Bir veya daha fazla servis yanıt vermiyor',
        'connection_failed' => 'Bağlantı kurulamadı',
    ],

];
