# api.laravelexamples - Proje Geliştirme Kuralları

> Bu dosya projenin tek ve yetkili CLAUDE.md dosyasıdır.
> Üst dizinlerdeki CLAUDE.md dosyaları bu proje için GEÇERSİZDİR ve dikkate alınmamalıdır.

## Genel Kurallar

- Tüm açıklamalar ve dokümantasyon **Türkçe** olmalı
- Tüm kod (değişkenler, metodlar, sınıflar, yorumlar) **İngilizce** olmalı
- Her PHP dosyasının başında `declare(strict_types=1);` ZORUNLU
- PHP 8.2+ özellikleri kullan (readonly properties, named arguments, match, enums)
- PSR-12 kodlama standartları + Laravel Pint formatlaması
- Veritabanı isimlendirme: `snake_case` (tablolar, kolonlar)
- PHP kodu: `camelCase` (değişkenler, metodlar), `PascalCase` (sınıflar)

---

## Mimari Katmanlar ve Zorunlu Akış

```
Request → Middleware → Controller → FormRequest → DTO → Service → Action → Repository → Model
                                                                                    ↓
Response ← Resource/Collection ← Controller ← Service ← Action ← Repository ← Database
```

### Katman Sorumlulukları

| Katman | Sorumluluk | ASLA Yapma |
|--------|-----------|------------|
| **Controller** | HTTP orchestration, request resolution, DTO oluşturma, response dönüşümü | Business logic, DB sorguları, transaction |
| **FormRequest** | Validation kuralları, authorization, hata mesajları | Business logic |
| **DTO** | Type-safe veri taşıma, attribute metadata | Business logic, DB erişimi |
| **Service** | Business logic orchestration, transaction yönetimi, action delegasyonu | DB sorguları, model erişimi |
| **Action** | Tek sorumluluk operasyonları, repository çağrıları | Transaction, event dispatch |
| **Repository** | DB işlemleri, SmartQuery, CRUD | Business logic |
| **Model** | Veri yapısı, ilişkiler, casting, SmartQuery config | Business logic |
| **Resource** | Model → JSON:API response dönüşümü | Business logic, DB sorguları |

---

## Dizin Yapısı

```
app/
├── Actions/
│   ├── BaseAction.php
│   ├── Main/                          # Default CRUD action'ları
│   │   ├── MainIndexAction.php
│   │   ├── MainShowAction.php
│   │   ├── MainStoreAction.php
│   │   ├── MainUpdateAction.php
│   │   └── MainDestroyAction.php
│   └── {Module}/{SubModule}/          # Modül-spesifik action'lar
│       ├── {Model}IndexAction.php
│       ├── {Model}ShowAction.php
│       ├── {Model}StoreAction.php
│       ├── {Model}UpdateAction.php
│       └── {Model}DestroyAction.php
├── Attributes/Model/                  # PHP 8 Attributes (metadata)
│   ├── ActionType.php
│   ├── FormField.php
│   └── TableColumn.php
├── DataTransferObjects/
│   ├── BaseDTO.php
│   └── {Module}/{SubModule}/
│       └── {Model}DTO.php
├── Exceptions/
│   ├── BaseException.php
│   ├── NotFoundException.php
│   └── {Module}/{SubModule}/
│       └── {Model}NotFoundException.php
├── Http/
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── HealthController.php       # Servis sağlık kontrolü (DB, Cache)
│   │   ├── MainController.php         # Generic CRUD (dynamic model)
│   │   ├── PivotController.php        # Pivot ilişkiler
│   │   └── {Module}/{SubModule}/
│   │       └── {Model}Controller.php
│   ├── Middleware/
│   │   ├── AssignRequestId.php        # X-Request-Id header
│   │   └── ValidateModule.php         # Dynamic model resolution
│   ├── Requests/
│   │   ├── BaseRequest.php
│   │   ├── BaseIndexRequest.php       # fields, include, sort, limit, filter
│   │   ├── BaseShowRequest.php
│   │   ├── BaseStoreRequest.php
│   │   ├── BaseUpdateRequest.php
│   │   ├── BaseDestroyRequest.php     # ids (toplu silme)
│   │   ├── BaseFieldUpdateRequest.php # field + value (tekil alan güncelleme)
│   │   └── {Module}/{SubModule}/
│   │       ├── {Model}IndexRequest.php
│   │       ├── {Model}ShowRequest.php
│   │       ├── {Model}StoreRequest.php
│   │       ├── {Model}UpdateRequest.php
│   │       └── {Model}DestroyRequest.php
│   └── Resources/
│       ├── BaseResource.php           # JSON:API format (type, id, attributes, relationships, links)
│       ├── BaseCollection.php
│       └── {Module}/{SubModule}/
│           ├── {Model}Resource.php
│           └── {Model}Collection.php
├── Models/
│   ├── BaseModel.php
│   └── {Module}/{SubModule}/
│       └── {Model}Model.php
├── Observers/
│   └── BaseModelObserver.php          # Audit fields (created_by, updated_by)
├── Providers/
│   ├── AppServiceProvider.php         # Rate limiting
│   └── RepositoryServiceProvider.php  # Repository bindings + dynamic service
├── Repositories/
│   ├── BaseRepository.php             # CRUD + SmartQuery
│   ├── BaseRepositoryCache.php        # Cache decorator (Redis tag-based)
│   ├── BaseRepositoryInterface.php
│   └── {Module}/{SubModule}/
│       ├── {Model}Repository.php
│       ├── {Model}RepositoryCache.php
│       └── {Model}RepositoryInterface.php
├── Services/
│   ├── BaseService.php                # Transaction + action orchestration
│   └── {Module}/{SubModule}/
│       └── {Model}Service.php
├── SmartQuery/                        # Query builder sistemi
│   ├── SmartQuery.php
│   ├── SmartQueryRequest.php
│   ├── Builders/
│   │   ├── Fields/AllowedField.php
│   │   ├── Filters/                   # ExactFilter, PartialFilter, OperatorFilter, vb.
│   │   ├── Includes/                  # RelationshipInclude, CountInclude, ExistsInclude
│   │   └── Sorts/                     # DefaultSort, CallbackSort
│   ├── Concerns/                      # FiltersQuery, IncludesRelationships, SelectsFields, SortsQuery
│   ├── Enums/                         # FilterOperator, SortDirection
│   ├── Exceptions/                    # InvalidFilterQuery, InvalidSortQuery, vb.
│   └── Support/                       # DTOMapper, ModelHydrator, QueryScopes
├── Support/
│   ├── DTOFactory.php                 # Request → DTO dönüşümü
│   ├── MetadataResolver.php           # DTO attribute metadata okuma (reflection + cache)
│   └── ResponseReference.php          # API response metadata (timestamp, version, request_id)
└── Traits/
    ├── HasActionResolver.php          # Controller'da request/DTO resolution
    ├── HasFieldMetadata.php           # Model'de DTO'dan metadata uygulama
    ├── HasFieldSelection.php          # JSON:API sparse fieldset
    ├── HasLocaleDateFormat.php        # Locale-based date formatting
    ├── HasQueryContext.php            # Request → query context parsing
    ├── HasRelationshipSeparation.php  # Attributes/relationships ayırma
    ├── HasResourceLinks.php           # JSON:API links (self)
    ├── HasSmartQuery.php              # SmartQuery builder entegrasyonu
    └── HasSmartQueryConfig.php        # Model SmartQuery config accessor'ları
```

---

## Base Class Kuralları (KRİTİK)

### BaseModel

**Dosya:** `app/Models/BaseModel.php`

BaseModel `abstract class` olup şunları sağlar:
- `SoftDeletes` trait (deleted_at)
- `HasFactory`, `HasFieldMetadata`, `HasLocaleDateFormat`, `HasSmartQueryConfig` trait'leri
- `BaseModelObserver` ile audit fields (created_by, updated_by)
- `$connection = 'conn_mysql'`
- `$keyType = 'int'`, `$incrementing = true` (integer auto-increment PK)
- `createdBy()`, `updatedBy()` → BelongsTo UserModel

**Child Model ZORUNLU tanımlamaları:**
```php
protected $table = 'cat_product';              // Tablo adı (prefix_entity)
protected $primaryKey = 'product_id';          // Custom PK ({entity}_id)
protected static ?string $fieldSource = ProductDTO::class;  // DTO bağlantısı
```

**Child Model İSTEĞE BAĞLI tanımlamaları:**
```php
protected array $allowedRelations = [...];     // API'den include edilebilecek ilişkiler
protected string $defaultSorting = '-created_at';  // Varsayılan sıralama

protected $casts = [                           // Tip dönüşümleri
    'price' => 'decimal:2',
    'is_active' => 'boolean',
];

public function getAllowedFilters(): array      // SmartQuery filter config
{
    return [
        'name',                                // Partial (default)
        AllowedFilter::exact('category_id'),   // Exact match
        AllowedFilter::trashed(),              // Soft delete filtresi
    ];
}
```

**Tablo İsimlendirme Konvansiyonu:**
- Format: `{modül_kısaltması}_{entity}` → `cat_product`, `cat_brand`, `cnt_page`
- Alt tablo: `{modül_kısaltması}_{parent}_{child}` → `cat_product_image`, `cat_product_translation`

**Primary Key Konvansiyonu:**
- Format: `{entity}_id` → `product_id`, `brand_id`, `category_id`
- Tip: `int` (auto-increment)

**UserModel İSTİSNA:** Authenticatable'dan extend eder, BaseModel'den değil. `HasFieldMetadata` trait'ini kullanır.

---

### BaseController

**Dosya:** `app/Http/Controllers/BaseController.php`

Trait'ler: `AuthorizesRequests`, `ValidatesRequests`, `HasActionResolver`, `HasQueryContext`

**Constructor parametreleri:**
```php
public function __construct(
    protected ?BaseService $service = null,
    protected string $resourceClass = '',
    protected string $collectionClass = '',
    protected array $requests = [],     // action → RequestClass mapping
    protected array $dtos = [],         // action → DTOClass mapping
)
```

**Sağladığı HTTP metodları:** `index()`, `show(int $id)`, `store()`, `update(int $id)`, `patch(int $id)`, `destroy(int $id)`

**Child Controller'da sadece constructor tanımla:**
```php
class ProductController extends BaseController
{
    public function __construct(ProductService $service)
    {
        parent::__construct(
            service: $service,
            resourceClass: ProductResource::class,
            collectionClass: ProductCollection::class,
            requests: [
                'index' => ProductIndexRequest::class,
                'show' => ProductShowRequest::class,
                'store' => ProductStoreRequest::class,
                'update' => ProductUpdateRequest::class,
                'fieldUpdate' => BaseFieldUpdateRequest::class,
                'destroy' => ProductDestroyRequest::class,
            ],
            dtos: [
                'store' => ProductDTO::class,
                'update' => ProductDTO::class,
            ],
        );
    }
}
```

---

### BaseService

**Dosya:** `app/Services/BaseService.php`

- Constructor: `BaseRepositoryInterface $repository`, `array $actions = []`
- Boş action array'i gelirse `Main*Action` class'larını kullanır (default CRUD)
- `store()`, `update()`, `destroy()` → `DB::transaction()` içinde çalışır
- `destroyMany(array $criteria)` → toplu silme desteği

**Child Service'de action override:**
```php
class ProductService extends BaseService
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository, [
            'index' => new ProductIndexAction($repository),
            'show' => new ProductShowAction($repository),
            'store' => new ProductStoreAction($repository),
            'update' => new ProductUpdateAction($repository),
            'destroy' => new ProductDestroyAction($repository),
        ]);
    }
}
```

---

### BaseAction

**Dosya:** `app/Actions/BaseAction.php`

Minimal abstract class: sadece `BaseRepositoryInterface $repository` constructor injection.

**Main Actions (Default CRUD):** `MainIndexAction`, `MainShowAction`, `MainStoreAction`, `MainUpdateAction`, `MainDestroyAction` → direkt repository metodu çağırır.

**Child Action:** Main action'dan extend eder. Modül-spesifik logic gerekirse override et.

---

### BaseRepository

**Dosya:** `app/Repositories/BaseRepository.php`

- `HasSmartQuery` trait'i ile akıllı sorgu desteği
- Metodlar: `paginate()`, `findById()`, `all()`, `create()`, `update()`, `delete()`, `deleteMany()`, `findBy()`, `getBy()`
- `paginate()` → `buildSmartQuery()` kullanır (filter, sort, include, field selection otomatik)
- `findById()` → `resolveIncludes()` ile eager loading

---

### BaseRepositoryCache (Cache Decorator)

**Dosya:** `app/Repositories/BaseRepositoryCache.php`

- `BaseRepositoryInterface` implement eder (aynı interface)
- Repository'yi wrap eder (Decorator Pattern)
- `HasCacheStrategy` trait'i ile cache altyapısı (key build, normalize, cached/invalidating)
- Redis tag-based caching (`Cache::tags()`)
- Cache key: `{table}.{method}.{hash}`
- TTL: 3600 saniye
- `create()`, `update()`, `delete()`, `deleteMany()` → cache flush

**Child Cache:**
```php
class ProductRepositoryCache extends BaseRepositoryCache implements ProductRepositoryInterface
{
    public function __construct(ProductRepository $repository, ProductModel $model)
    {
        parent::__construct($repository, $model->getTable());
    }
}
```

---

### BaseRequest

**Dosya:** `app/Http/Requests/BaseRequest.php`

- `FormRequest` extend eder
- `authorize()` → true (varsayılan)
- `failedValidation()` → JSON hata response (422, Türkçe mesaj)
- Hata formatı: `{ success: false, message, error_code: 'VALIDATION_ERROR', errors, reference }`

**Özel Base Request'ler:**
- `BaseIndexRequest` → fields, include, sort, limit, filter kuralları (Türkçe mesajlar)
- `BaseDestroyRequest` → ids array desteği (toplu silme)
- `BaseFieldUpdateRequest` → field + value (tekil alan güncelleme, fillable kontrolü)

---

### BaseDTO

**Dosya:** `app/DataTransferObjects/BaseDTO.php`

- `fromArray(array): static` → array'den DTO oluştur
- `toArray(): array` → tüm property'leri array'e dönüştür
- `only(): array` → sadece null olmayan değerleri dön

**DTO Attribute Pattern (PHP 8 Attributes):**
```php
class ProductDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $product_id = null,

        #[FormField(type: 'text', sort_order: 2)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $name = null,
    ) {}
}
```

**Attribute açıklamaları:**
- `#[FormField]` → Form input tipi, sıralama, ilişki bilgisi. Tipler: text, textarea, richtext, number, email, password, boolean, select, image, datetime
- `#[TableColumn]` → Tablo görünürlüğü. Actions: showing, filtering, sorting
- `#[ActionType]` → Hangi CRUD action'larda bu alan mevcut: index, show, store, update, destroy

**DTO Kuralları:**
- Tüm property'ler `public readonly` ve nullable (`?type`)
- Property adları veritabanı kolon adlarıyla birebir eşleşir (snake_case)
- Her DTO BaseDTO'dan extend eder
- Alt model DTO'ları `Subs/` dizininde: `ProductImageDTO`, `ProductTranslationDTO`

---

### BaseResource / BaseCollection

**Dosya:** `app/Http/Resources/BaseResource.php`, `app/Http/Resources/BaseCollection.php`

BaseResource trait'leri: `HasFieldSelection`, `HasRelationshipSeparation`, `HasResourceLinks`

**JSON:API Response Formatı:**
```json
{
    "data": {
        "type": "cat_product",
        "id": "1",
        "attributes": { "name": "...", "price": 99.99 },
        "relationships": { "category": {...} },
        "links": { "self": "/api/v1/catalog/product/1" }
    },
    "success": true,
    "reference": {
        "message": "Record retrieved successfully",
        "status_code": 200,
        "timestamp": "2026-02-22T10:30:00Z",
        "version": "v1",
        "request_id": "uuid"
    }
}
```

**Child Resource:**
```php
class ProductResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'category' => $this->whenLoaded('category'),
            // ...
        ];
    }
}
```

**Child Collection:**
```php
class ProductCollection extends BaseCollection
{
    public $collects = ProductResource::class;
}
```

---

### BaseException

**Dosya:** `app/Exceptions/BaseException.php`

- `render()` → JSON response + otomatik loglama
- Debug modda: exception class, file, line bilgisi
- 500+ → `Log::error()`, diğerleri → `Log::warning()`

**Child Exception:**
```php
class ProductNotFoundException extends BaseException
{
    protected int $statusCode = 404;
    protected string $errorCode = 'PRODUCT_NOT_FOUND';
}
```

---

## SmartQuery Sistemi

SmartQuery, API sorgularını yöneten in-house query builder'dır.

### Kullanım (Repository'de otomatik, HasSmartQuery trait'i)
```php
$this->buildSmartQuery()->paginate($perPage);
```

### API Query Parametreleri
```
GET /api/v1/catalog/product?filter[name]=laptop&filter[is_active]=1&sort=-created_at,name&include=category,images&fields[cat_product]=name,price&limit=25
```

| Parametre | Açıklama | Örnek |
|-----------|----------|-------|
| `filter[field]` | Filtreleme | `filter[name]=laptop` |
| `sort` | Sıralama (- prefix = DESC) | `sort=-created_at,name` |
| `include` | Eager loading | `include=category,images` |
| `fields[table]` | Sparse fieldset | `fields[cat_product]=name,price` |
| `limit` | Sayfa başı kayıt | `limit=25` |

### Filter Tipleri
```php
// Model'de getAllowedFilters():
AllowedFilter::partial('name')          // LIKE '%value%'
AllowedFilter::exact('category_id')     // WHERE = value
AllowedFilter::operator('price', FilterOperator::GREATER_THAN)  // WHERE > value
AllowedFilter::scope('active')          // Model scope çağırır
AllowedFilter::belongsTo('brand')       // Foreign key filter
AllowedFilter::trashed()                // with/only/without trashed
AllowedFilter::callback('custom', fn($q, $v) => ...)  // Custom closure
```

### Include Türleri
```
?include=category              → with('category')
?include=imagesCount           → withCount('images')
?include=reviewsExists         → withExists('reviews')
```

---

## Dynamic CRUD Sistemi

### ValidateModule Middleware
URL path'inden model sınıfını otomatik çözer:
- `/api/v1/catalog/product` → `App\Models\Catalog\Product\ProductModel`
- `/api/v1/content/page` → `App\Models\Content\Page\PageModel`
- Cache'li (3600s Redis + memory)

### MainController + Dynamic Service
RepositoryServiceProvider'da:
```
Request → modelClass → BaseRepository(model) → BaseRepositoryCache(repo, table) → BaseService(cachedRepo) → MainController
```
Bu sayede her yeni model için otomatik CRUD endpoint'i oluşur.

### Üç Seviyeli Route Yapısı
1. **Statik Routes:** `/v1/catalog/product` → ProductController (özel iş mantığı olan modüller)
2. **Pivot Routes:** `/{path}/{id}/{relation}/{relationId}` → PivotController
3. **Generic Routes:** `/{path}` → MainController (dynamic model, catch-all)

---

## Trait Sistemi

| Trait | Kullanıldığı Yer | Görev |
|-------|------------------|-------|
| `HasActionResolver` | BaseController | Request/DTO class resolution, DTOFactory entegrasyonu |
| `HasQueryContext` | BaseController | Request → query context (filter, sort, include, fields, limit) |
| `HasFieldMetadata` | BaseModel | DTO'dan fillable, allowedFiltering/Sorting/Showing otomatik uygulama |
| `HasSmartQueryConfig` | BaseModel | SmartQuery config property getter'ları |
| `HasCacheStrategy` | BaseRepositoryCache | Cache altyapısı: key build, normalize, cached/invalidating |
| `HasSmartQuery` | BaseRepository | SmartQuery builder oluşturma, include resolution |
| `HasFieldSelection` | BaseResource | JSON:API sparse fieldset |
| `HasRelationshipSeparation` | BaseResource | Attributes/relationships ayırma |
| `HasResourceLinks` | BaseResource | JSON:API type, id, links oluşturma |
| `HasLocaleDateFormat` | BaseModel | created_at/updated_at locale-based formatting |

---

## Support Sınıfları

| Sınıf | Görev |
|-------|-------|
| `DTOFactory` | Request'ten DTO instantiate (MetadataResolver + type casting) |
| `MetadataResolver` | DTO constructor'dan reflection ile attribute metadata okuma (3-level cache) |
| `ResponseReference` | API response metadata: message, status_code, timestamp, version, request_id, response_time |

---

## PHP 8 Attribute Sistemi

Model metadata'sı DTO constructor parameter'lerine attribute olarak eklenir. Bu sistem form oluşturma, tablo konfigürasyonu ve action bazlı alan kontrolü sağlar.

### ActionType
```php
#[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
```
Bu alan hangi CRUD action'larda dahil edilecek.

### FormField
```php
#[FormField(type: 'select', sort_order: 5, relationship: ['model' => 'CategoryModel', 'label' => 'name'])]
```
Form input tipi ve ayarları.

### TableColumn
```php
#[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
```
Tablo listesinde gösterilme, filtreleme ve sıralama izinleri.

---

## BaseModelObserver

**Dosya:** `app/Observers/BaseModelObserver.php`

- `creating`: UUID generate (uuid field), created_by/updated_by set
- `updating`: updated_by set
- BaseModel'in `booted()` metodunda otomatik register edilir

---

## Veritabanı Kuralları

- Bağlantı: `conn_mysql` (config/database.php)
- Cache: Redis (config/cache.php, default: redis)
- Her tabloda ZORUNLU kolonlar:
  - `{entity}_id` (INT, PK, auto-increment)
  - `created_by` (INT, nullable, FK → users)
  - `updated_by` (INT, nullable, FK → users)
  - `created_at`, `updated_at` (TIMESTAMP)
  - `deleted_at` (TIMESTAMP, nullable, soft delete)
- Tablo prefix'leri: `cat_` (catalog), `cnt_` (content)
- **Foreign Key constraint KULLANILMAZ** — ilişkiler uygulama katmanında (Eloquent relationships) yönetilir, migration'larda `->foreign()` veya `->constrained()` tanımlanmaz
- Migration'larda **enum kolon tipi KULLANILMAZ** — sabit değerler ileride referans tablo ilişkisine dönüştürülecektir. Bunun yerine `string` veya `tinyInteger` kullan
- Index'leri migration'larda ekle

---

## API Response Standartları

### Başarı (Resource)
```json
{
    "data": { "type": "...", "id": "...", "attributes": {}, "relationships": {}, "links": {} },
    "success": true,
    "reference": { "message": "...", "status_code": 200, "timestamp": "...", "version": "v1", "request_id": "..." }
}
```

### Başarı (Collection)
```json
{
    "data": [...],
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
    "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 75 },
    "success": true,
    "reference": { ... }
}
```

### Hata
```json
{
    "success": false,
    "message": "Hata mesajı",
    "error_code": "PRODUCT_NOT_FOUND",
    "reference": { "message": "...", "status_code": 404, "timestamp": "...", "version": "v1" }
}
```

### Validation Hatası (422)
```json
{
    "success": false,
    "message": "Doğrulama hatası",
    "error_code": "VALIDATION_ERROR",
    "errors": { "name": ["Ürün adı zorunludur"] },
    "reference": { ... }
}
```

---

## İsimlendirme Konvansiyonları

| Tip | Format | Örnek |
|-----|--------|-------|
| Model | `{Entity}Model` | `ProductModel`, `BrandModel` |
| Controller | `{Entity}Controller` | `ProductController` |
| Service | `{Entity}Service` | `ProductService` |
| Action | `{Entity}{Action}Action` | `ProductStoreAction` |
| Repository | `{Entity}Repository` | `ProductRepository` |
| RepositoryCache | `{Entity}RepositoryCache` | `ProductRepositoryCache` |
| RepositoryInterface | `{Entity}RepositoryInterface` | `ProductRepositoryInterface` |
| DTO | `{Entity}DTO` | `ProductDTO` |
| Resource | `{Entity}Resource` | `ProductResource` |
| Collection | `{Entity}Collection` | `ProductCollection` |
| Request | `{Entity}{Action}Request` | `ProductStoreRequest` |
| Exception | `{Entity}NotFoundException` | `ProductNotFoundException` |
| Factory | `{Entity}ModelFactory` | `ProductModelFactory` |
| Tablo | `{prefix}_{entity}` | `cat_product`, `cnt_page` |
| Primary Key | `{entity}_id` | `product_id`, `brand_id` |

---

## Yeni Modül Oluşturma Şablonu

Yeni bir modül eklerken şu dosyaları oluştur (örnek: `Catalog/Brand`):

### 1. Model
```php
<?php
declare(strict_types=1);
namespace App\Models\Catalog\Brand;

use App\DataTransferObjects\Catalog\Brand\BrandDTO;
use App\Models\BaseModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;

class BrandModel extends BaseModel
{
    protected $table = 'cat_brand';
    protected $primaryKey = 'brand_id';
    protected static ?string $fieldSource = BrandDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'name', 'slug',
            AllowedFilter::exact('is_active'),
            AllowedFilter::trashed(),
        ];
    }

    protected array $allowedRelations = ['products', 'createdBy', 'updatedBy'];
    protected string $defaultSorting = '-created_at';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // İlişkiler...
}
```

### 2. DTO (Attributes ile)
```php
<?php
declare(strict_types=1);
namespace App\DataTransferObjects\Catalog\Brand;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

class BrandDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $brand_id = null,

        #[FormField(type: 'text', sort_order: 2)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $name = null,

        // Diğer alanlar...
    ) {}
}
```

### 3. Repository + Cache + Interface
```php
// BrandRepositoryInterface extends BaseRepositoryInterface
// BrandRepository extends BaseRepository implements BrandRepositoryInterface
// BrandRepositoryCache extends BaseRepositoryCache implements BrandRepositoryInterface
```

### 4. Service + Actions
```php
// BrandService extends BaseService (actions override)
// Brand{Action}Action extends Main{Action}Action
```

### 5. Controller + Requests + Resources
```php
// BrandController extends BaseController (constructor config)
// Brand{Action}Request extends Base{Action}Request
// BrandResource extends BaseResource
// BrandCollection extends BaseCollection
```

### 6. RepositoryServiceProvider'a Binding Ekle
```php
$this->app->bind(BrandRepositoryInterface::class, function ($app) {
    return new BrandRepositoryCache(
        new BrandRepository(new BrandModel()),
        new BrandModel(),
    );
});
```

### 7. Route Tanımla (eğer custom controller gerekiyorsa)
Generic CRUD → MainController otomatik çalışır (route tanıma gerek yok).
Custom controller → `routes/api/v1.php` dosyasına ekle.

---

## Rate Limiting

Tüm limitler `.env` üzerinden yapılandırılabilir:

| Limiter | Env Değişkeni | Varsayılan | Kimlik |
|---------|--------------|------------|--------|
| `api` | `RATE_LIMIT_API` | 60/dakika | Auth user ID veya IP |
| `api-public` | `RATE_LIMIT_API_PUBLIC` | 30/dakika | IP |
| `api-write` | `RATE_LIMIT_API_WRITE` | 20/dakika | Auth user ID veya IP |
| `api-auth` | `RATE_LIMIT_API_AUTH` | 10/dakika | IP |

**Route atamaları:**
- GET endpoint'leri → `api` (varsayılan, middleware'de tanımlı)
- POST/PUT/PATCH/DELETE → `throttle:api-write` (route gruplarında)
- Auth endpoint'leri → `throttle:api-auth` (login, register vb.)

Limit aşımında standart JSON hata response döner (`429 TOO_MANY_REQUESTS`).

---

## Health Check

**Endpoint:** `GET /api/v1/health`
**Controller:** `app/Http/Controllers/HealthController.php` (Invokable)

Veritabanı ve cache servislerinin durumunu kontrol eder. Rate limit uygulanmaz.

**Response (200 veya 503):**
```json
{
    "success": true,
    "data": {
        "status": "healthy",
        "checks": {
            "database": { "status": "pass", "latency_ms": 5 },
            "cache": { "status": "pass", "driver": "redis", "latency_ms": 2 }
        }
    },
    "reference": { "message": "Tüm servisler çalışıyor", "status_code": 200 }
}
```

**Kontrol edilen servisler:**
- `database` → `DB::connection()->getPdo()` ile bağlantı testi
- `cache` → `Cache::put/get/forget` ile okuma/yazma testi

Her kontrol bağımsız çalışır; bir servis fail olursa HTTP 503 döner, diğer kontroller çalışmaya devam eder.

---

## Exception Handling (bootstrap/app.php)

| Exception | Status | Error Code |
|-----------|--------|------------|
| NotFoundHttpException | 404 | RESOURCE_NOT_FOUND |
| ModelNotFoundException | 404 | RESOURCE_NOT_FOUND |
| TooManyRequestsHttpException | 429 | TOO_MANY_REQUESTS |
| AuthenticationException | 401 | UNAUTHORIZED |
| HttpException | Dinamik | SERVER_ERROR |
| Throwable | 500 | INTERNAL_SERVER_ERROR |

Tüm exception mesajları `__()` helper ile lokalize edilir (`lang/tr/api.php`).

---

## Kod Üretim Kuralları

1. Base class'lardan KOD KOPYALAMA - sadece extend et
2. Her dosyada `declare(strict_types=1);` zorunlu
3. Constructor property promotion kullan
4. Tüm parametrelerde type hint zorunlu
5. `readonly` properties kullan (DTO'larda)
6. Named arguments kullan (constructor çağrılarında)
7. Validation mesajları Türkçe
8. Sadece modül-spesifik business logic ekle
9. Transaction sadece Service katmanında (BaseService'de zaten var)
10. Cache invalidation BaseRepositoryCache'de otomatik
11. **PHP docblock ve yorum YAZILMAZ** — Kod kendi kendini açıklamalı (self-documenting). PHPDoc, inline comment, block comment KULLANILMAZ. Sadece karmaşık algoritmalar veya geçici workaround'lar için kısa, İngilizce tek satırlık yorum kabul edilir.

## Yapılmaması Gerekenler

1. Base class'lardan kod kopyalama
2. Controller'da business logic
3. Service'de DB sorgusu
4. Action'da transaction/event dispatch
5. Repository'de business logic
6. Model'de BaseModel'deki şeyleri tekrar tanımlama (audit fields, soft delete, UUID)
7. DTO'da business logic
8. Resource'da DB sorgusu
9. Gereksiz base method override
10. Base request'i extend etmeden FormRequest oluşturma
11. PHP docblock veya gereksiz yorum ekleme
