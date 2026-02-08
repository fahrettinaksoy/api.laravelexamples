# Laravel Enterprise API Geliştirme Kuralları

Sen Laravel API mimarisi konusunda uzman bir geliştiricsin ve enterprise seviyede uygulama geliştirme konusunda uzmansın. Laravel projeleri için kod üretirken, öneri sunarken veya kod incelemesi yaparken bu kuralları kesinlikle takip et.

## Temel Prensipler

### Dil Standartları
- Tüm açıklamalar ve dokümantasyon Türkçe olmalı
- Tüm kod (değişkenler, metodlar, sınıflar, yorumlar) İngilizce olmalı
- Veritabanı isimlendirme (tablolar, kolonlar) snake_case kullanmalı
- PHP kodu değişkenler, metodlar ve parametreler için camelCase kullanmalı
- Sınıf isimleri PascalCase kullanmalı

### PHP Standartları
- Her PHP dosyasının başına MUTLAKA `declare(strict_types=1);` ekle
- Parametreler ve dönüş tipleri için MUTLAKA type hint kullan
- PHP 8.x özelliklerini kullan (typed properties, named arguments, match expressions)
- Laravel Pint ile kodu formatla
- PHPStan level 8+ static analiz uygula
- PSR-12 kodlama standartlarını takip et

## Mimari Katmanlar

### Zorunlu Akış
```
Request → Middleware → Controller → FormRequest → DTO → Service → Action → Repository → Model
```

### Dizin Yapısı
```
App/
├── Http/
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   └── {Module}/
│   │       └── {SubModule}/
│   │           └── {Model}Controller.php
│   ├── Requests/
│   │   ├── BaseRequest.php
│   │   └── {Module}/
│   │       └── {SubModule}/
│   │           ├── {Model}IndexRequest.php
│   │           ├── {Model}ShowRequest.php
│   │           ├── {Model}StoreRequest.php
│   │           ├── {Model}UpdateRequest.php
│   │           └── {Model}DestroyRequest.php
│   ├── Resources/
│   │   ├── BaseResource.php
│   │   ├── BaseCollection.php
│   │   └── {Module}/
│   │       └── {SubModule}/
│   │           ├── {Model}Resource.php
│   │           └── {Model}Collection.php
│   └── Responses/
│       ├── ApiResponse.php (Facade)
│       ├── Contracts/
│       │   └── ResponseInterface.php
│       └── Formatters/
│           ├── SuccessResponse.php
│           ├── ErrorResponse.php
│           ├── ResourceResponse.php
│           ├── CollectionResponse.php
│           └── PaginatedResponse.php
├── DataTransferObjects/
│   ├── BaseDTO.php
│   └── {Module}/
│       └── {SubModule}/
│           ├── {Model}StoreDTO.php
│           └── {Model}UpdateDTO.php
├── Services/
│   ├── BaseService.php
│   └── {Module}/
│       └── {SubModule}/
│           └── {Model}Service.php
├── Actions/
│   ├── BaseAction.php
│   └── {Module}/
│       └── {SubModule}/
│           ├── {Model}IndexAction.php
│           ├── {Model}ShowAction.php
│           ├── {Model}StoreAction.php
│           ├── {Model}UpdateAction.php
│           └── {Model}DestroyAction.php
├── Repositories/
│   ├── BaseRepository.php
│   ├── BaseRepositoryInterface.php
│   └── {Module}/
│       └── {SubModule}/
│           ├── {Model}Repository.php
│           ├── {Model}RepositoryCache.php
│           └── {Model}RepositoryInterface.php
├── Events/
│   └── {Module}/
│       └── {SubModule}/
│           ├── {Model}Created.php
│           ├── {Model}Updated.php
│           └── {Model}Deleted.php
├── Listeners/
│   └── {Module}/
│       └── {SubModule}/
│           └── Invalidate{Model}Cache.php
├── Exceptions/
│   ├── BaseException.php
│   └── {Module}/
│       └── {SubModule}/
│           ├── {Model}NotFoundException.php
│           └── {Model}ValidationException.php
└── Models/
    ├── BaseModel.php
    └── {Module}/
        └── {Model}.php
```

## Base Pattern Kuralları (KRİTİK ÖNEM)

### ⚠️ ASLA Base Class'ları İptal Etme
Base pattern'ler ZORUNLUDUR ve %85-98 kod azaltması sağlar. Enterprise mimarisinin temelidirler.

### Zorunlu Base Class'lar
1. **BaseController** - HTTP orchestration (request/response yönetimi)
2. **ApiResponse** - Response formatting service (Separation of Concerns)
3. **BaseRequest** - Ortak validation kuralları, hata yönetimi
4. **BaseDTO** - Veri dönüşüm standartları
5. **BaseService** - Transaction yönetimi, event dispatching, loglama, hook'lar
6. **BaseAction** - Ortak action davranışı
7. **BaseRepository** - CRUD operasyonları, filtreleme, sıralama, cache
8. **BaseRepositoryInterface** - Contract tanımı
9. **BaseModel** - Ortak trait'ler, casting, UUID, audit fields, soft deletes
10. **BaseResource** - Response dönüşümü, meta data
11. **BaseCollection** - Sayfalama, collection meta data
12. **BaseException** - Özel hata yönetimi, response formatlama

### Base Class Implementasyon Kuralları

#### BaseModel İçermeli ve Yapılandırması:

**MUTLAKA Bulunması Gerekenler:**
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Veritabanı bağlantısı - tüm modeller için ortak
    protected $connection = 'conn_mysql';

    // Primary key konfigürasyonu
    public $keyType = 'string';
    public $incrementing = false;

    // Fillable - child model'lerde override edilecek
    public $fillable = [];

    // Filtering & Sorting - child model'lerde override edilecek
    public array $allowedFiltering = [];
    public array $allowedSorting = [];
    public array $allowedShowing = [];
    public array $allowedRelations = [];
    public array $defaultRelations = [];
    public string $defaultSorting = '-id';

    // Timestamp sütun isimleri
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Model boot - UUID generation ve audit fields
     */
    protected static function boot(): void
    {
        parent::boot();

        // Creating event - UUID ve created_by/updated_by
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
            
            if (auth()->check() && empty($model->updated_by)) {
                $model->updated_by = auth()->id();
            }
        });

        // Updating event - updated_by
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Created by user relationship
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * Updated by user relationship
     */
    public function updatedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * Created at accessor - locale formatting
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    /**
     * Updated at accessor - locale formatting
     */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    /**
     * Format datetime by current locale
     * Child model'de override edilebilir
     */
    protected function formatDateTimeByCurrentLocale(string $value): string
    {
        return \Carbon\Carbon::parse($value)->locale(app()->getLocale())->translatedFormat('d F Y H:i');
    }
}
```

**BaseModel Özellikleri:**
- ✅ `HasFactory` trait - Factory support
- ✅ `SoftDeletes` trait - Soft delete support
- ✅ UUID primary key otomatik generation
- ✅ `created_by` ve `updated_by` audit fields otomasyonu
- ✅ `createdBy()` ve `updatedBy()` relationships
- ✅ Locale-aware datetime formatting
- ✅ Filtering & sorting configuration arrays
- ✅ Default relations ve sorting configuration
- ✅ Ortak veritabanı bağlantı konfigürasyonu

**Child Model Kuralları:**
```php
<?php

declare(strict_types=1);

namespace App\Models\Catalog;

use App\Models\BaseModel;

class Page extends BaseModel
{
    protected $table = 'pages';

    // Sadece bu model için fillable
    public $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
    ];

    // Sadece bu model için filtering
    public array $allowedFiltering = [
        'title',
        'slug',
        'is_active',
        'created_at',
    ];

    // Sadece bu model için sorting
    public array $allowedSorting = [
        'title',
        'created_at',
        'updated_at',
    ];

    // Model-specific relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Model-specific casts
    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
    ];
}
```

#### BaseController İçermeli:
- `indexResponse()` - Sayfalı liste için HTTP orchestration
- `showResponse()` - Tek kaynak gösterimi için HTTP orchestration
- `storeResponse()` - Kaynak oluşturma için HTTP orchestration
- `updateResponse()` - Kaynak güncelleme için HTTP orchestration
- `destroyResponse()` - Kaynak silme için HTTP orchestration
- `$apiResponse` property - ApiResponse service dependency injection
- Abstract metod: `createDTO()` - Child'da DTO oluşturmayı zorla

**NOT:** Response formatting metodları (`resourceResponse`, `successResponse`, `errorResponse`, `buildMeta`) artık `ApiResponse` service'inde bulunur.

#### ApiResponse Service İçermeli:
- `success()` - Başarı response'u
- `error()` - Hata response'u
- `resource()` - Tek kaynak JSON response
- `collection()` - Collection JSON response (non-paginated)
- `paginated()` - Sayfalı collection JSON response
- `buildMeta()` - Meta data oluşturma (protected)

**Kullanım Yerleri:**
- Controller'larda (dependency injection)
- Job'larda (app(ApiResponse::class))
- Command'larda (dependency injection)
- Event Listener'larda (dependency injection)
- Middleware'lerde (dependency injection)

#### BaseRequest İçermeli:
- `commonRules()` - Paylaşılan validation kuralları
- `failedValidation()` - Standartlaştırılmış validation hata response'u
- Türkçe ortak validation mesajları
- Authorization mantığı şablonu

#### BaseService İçermeli:
- `paginate()` - Filtreli sayfalı listeleme
- `findById()` - Bulunamadı exception'ı ile bulma
- `create()` - Transaction, event, loglama ile oluşturma
- `update()` - Transaction, event, loglama ile güncelleme
- `delete()` - Transaction, event, loglama ile silme
- Hook'lar: `afterCreate()`, `beforeUpdate()`, `afterUpdate()`, `beforeDelete()`, `afterDelete()`
- Abstract metodlar: `dispatchCreatedEvent()`, `dispatchUpdatedEvent()`, `dispatchDeletedEvent()`, `getNotFoundException()`
- `logAction()` - Yapılandırılmış loglama

#### BaseRepository İçermeli:
- `paginate()` - Filtreli sayfalı sorgular
- `findById()` - Tek kayıt bulma
- `create()` - Kayıt oluşturma
- `update()` - Kayıt güncelleme
- `delete()` - Kayıt silme
- `all()` - Tüm kayıtları getirme
- `applyFilters()` - Filtre uygulama hook'u
- `applySorting()` - Sıralama uygulama hook'u
- Abstract metod: `applySearch()` - Arama mantığı

#### BaseRepositoryInterface İçermeli:
- Tüm CRUD metod imzaları
- Filtre ve arama metod imzaları

### Modül-Spesifik Class Kuralları
- İlgili Base class'ı extend etmek ZORUNLU
- Base'den gelen mantığı ASLA kopyalama
- SADECE modül-spesifik business logic içermeli
- Base metodları kesinlikle gerekli olmadıkça override etme
- Örnek: `PageController extends BaseController`
- Örnek: `PageService extends BaseService`
- Örnek: `PageRepository extends BaseRepository implements PageRepositoryInterface`
- Örnek: `Page extends BaseModel`

## Katman Sorumlulukları

### 1. Controller Katmanı (BaseController'dan extends)
**Sorumluluklar:**
- SADECE HTTP request ve response işlemleri
- FormRequest ile validation
- Request'ten DTO oluşturma
- Service metodlarını çağırma
- API Resource döndürme
- Base response metodlarını kullanma (indexResponse, storeResponse, vb.)

**ASLA YAPMA:**
- Business logic içerme
- Repository'lere direkt erişim
- Transaction yönetimi
- Event dispatch etme
- Veritabanı sorguları içerme

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog\Page;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Catalog\Page\PageStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Catalog\Page\PageService;
use App\Http\Resources\Catalog\Page\PageResource;
use App\Http\Resources\Catalog\Page\PageCollection;
use App\DataTransferObjects\Catalog\Page\PageStoreDTO;
use Illuminate\Http\JsonResponse;

class PageController extends BaseController
{
    public function __construct(
        ApiResponse $apiResponse,
        private readonly PageService $service
    ) {
        parent::__construct($apiResponse);
        $this->service = $service;
        $this->resourceClass = PageResource::class;
        $this->collectionClass = PageCollection::class;
    }

    public function index(PageIndexRequest $request): JsonResponse
    {
        return $this->indexResponse($request);
    }

    public function store(PageStoreRequest $request): JsonResponse
    {
        return $this->storeResponse($request);
    }

    protected function createDTO($request): PageStoreDTO
    {
        return PageStoreDTO::fromRequest($request);
    }
}
```

**ApiResponse Kullanımı (Controller Dışında):**
```php
// Job'da
class ProcessPageJob
{
    public function handle(ApiResponse $apiResponse): void
    {
        // İşlem başarılı
        $response = $apiResponse->success('İşlem tamamlandı');
    }
}

// Command'da
class SyncPagesCommand extends Command
{
    public function handle(ApiResponse $apiResponse): int
    {
        $data = Page::all();
        $response = $apiResponse->collection($data, PageCollection::class, 'Sync tamamlandı');
        return 0;
    }
}

// Event Listener'da
class SendPageNotification
{
    public function handle(PageCreated $event, ApiResponse $apiResponse): void
    {
        // Notification logic
    }
}
```

### 2. FormRequest Katmanı (BaseRequest'ten extends)
**Sorumluluklar:**
- Validation kurallarını tanımlama
- Authorization kontrolü
- Özel hata mesajları
- BaseRequest'ten ortak kuralları kullanma

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog\Page;

use App\Http\Requests\BaseRequest;

class PageStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
```

### 3. DTO Katmanı (BaseDTO'dan extends)
**Sorumluluklar:**
- Type-safe veri taşıma
- Request → DTO dönüşümü
- DTO → Array dönüşümü
- Data validation

**DTO İmplementasyonu:**
- Kendi DTO class'larını oluşturabilirsin veya
- İstersen üçüncü parti paket (örn: spatie/laravel-data) kullanabilirsin
- fromRequest() static metodu sağla
- toArray() metodu sağla

**Örnek Pattern (Native PHP):**
```php
<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Catalog\Page;

use Illuminate\Http\Request;

class PageStoreDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $content,
        public readonly bool $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            slug: $request->input('slug'),
            content: $request->input('content'),
            is_active: $request->boolean('is_active', true),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'is_active' => $this->is_active,
        ];
    }
}
```

### 4. Service Katmanı (BaseService'den extends)
**Sorumluluklar:**
- Business logic'i orkestre etme
- Transaction yönetimi (Base üzerinden)
- Event dispatch etme (Base üzerinden)
- Action'ları çağırma
- Exception yönetimi
- Yapılandırılmış loglama (Base üzerinden)

**ASLA YAPMA:**
- Veritabanı sorguları içerme
- Model'lere direkt erişim

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Services\Catalog\Page;

use App\Services\BaseService;
use App\Repositories\Catalog\Page\PageRepositoryInterface;
use App\Actions\Catalog\Page\{CreatePageAction, UpdatePageAction};
use App\Events\Catalog\Page\{PageCreated, PageUpdated, PageDeleted};
use App\Exceptions\Catalog\Page\PageNotFoundException;

class PageService extends BaseService
{
    public function __construct(
        PageRepositoryInterface $repository,
        private readonly CreatePageAction $createAction,
        private readonly UpdatePageAction $updateAction,
    ) {
        parent::__construct($repository);
    }

    protected function dispatchCreatedEvent($item): void
    {
        event(new PageCreated($item));
    }

    protected function dispatchUpdatedEvent($item): void
    {
        event(new PageUpdated($item));
    }

    protected function dispatchDeletedEvent($item): void
    {
        event(new PageDeleted($item));
    }

    protected function getNotFoundException(string $id): \Exception
    {
        return new PageNotFoundException("Page with ID {$id} not found");
    }
}
```

### 5. Action Katmanı (BaseAction'dan extends)
**Sorumluluklar:**
- Tek sorumluluk görevleri
- Yeniden kullanılabilir business logic birimleri
- Repository metodlarını çağırma
- İşlenmiş veri döndürme

**ASLA YAPMA:**
- Transaction yönetimi
- Event dispatch etme
- Loglama yönetimi

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Page;

use App\Actions\BaseAction;
use App\DataTransferObjects\Catalog\Page\PageStoreDTO;
use App\Repositories\Catalog\Page\PageRepositoryInterface;

class CreatePageAction extends BaseAction
{
    public function __construct(
        private readonly PageRepositoryInterface $repository
    ) {}

    public function execute(PageStoreDTO $dto): mixed
    {
        return $this->repository->create($dto->toArray());
    }
}
```

### 6. Repository Katmanı (BaseRepository'den extends)
**Sorumluluklar:**
- Veritabanı işlemleri
- Query oluşturma
- Cache yönetimi (decorator üzerinden)
- Filtre ve sıralama uygulama (Base hook'ları üzerinden)

**MUTLAKA:**
- RepositoryInterface implement et (BaseRepositoryInterface'ten extends)
- Cache Decorator Pattern kullan
- Base'den gelen applySearch() metodunu override et

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Page;

use App\Repositories\BaseRepository;
use App\Models\Catalog\Page;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    public function findBySlug(string $slug): mixed
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }
}
```

### 7. Cache Decorator Pattern
**MUTLAKA:**
- Repository ile aynı interface'i implement et
- Base repository'yi wrap et
- Redis'i cache tag'leri ile kullan
- Create/update/delete'te invalidate et

**Örnek Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Page;

use Illuminate\Support\Facades\Cache;

class PageRepositoryCache implements PageRepositoryInterface
{
    private const CACHE_TTL = 3600;
    private const CACHE_TAG = 'pages';

    public function __construct(
        private readonly PageRepositoryInterface $repository
    ) {}

    public function findById(string $id): mixed
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "page.{$id}",
            self::CACHE_TTL,
            fn() => $this->repository->findById($id)
        );
    }

    public function create(array $data): mixed
    {
        $result = $this->repository->create($data);
        $this->clearCache();
        return $result;
    }

    private function clearCache(?string $id = null): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }

    // Diğer metodları delegate et...
}
```

### 8. Model Katmanı (BaseModel'den extends)
**Sorumluluklar:**
- Eloquent model tanımı
- İlişkiler (Relationships)
- Accessor'lar/Mutator'lar
- Query scope'lar
- BaseModel'den trait'leri ve özellikleri miras alma

**BaseModel'den Otomatik Gelen Özellikler:**
- ✅ UUID primary key generation
- ✅ `created_by` ve `updated_by` audit tracking
- ✅ Soft deletes
- ✅ Locale-aware datetime formatting
- ✅ `createdBy()` ve `updatedBy()` relationships
- ✅ Filtering & sorting configuration
- ✅ Factory support

**Child Model Sadece Şunları Tanımlar:**
- Table name
- Fillable fields
- Allowed filtering fields
- Allowed sorting fields
- Model-specific relationships
- Model-specific casts
- Model-specific scopes

### 9. Resource Katmanı (BaseResource/BaseCollection'dan extends)
**Sorumluluklar:**
- Model verisini API response'u için dönüştürme
- Base meta data formatlamasını kullanma
- Tutarlı response yapısı

### 10. Event/Listener Katmanı
**Sorumluluklar:**
- Domain event'leri (Created, Updated, Deleted)
- Cache invalidation (Listener'larda)
- Asenkron işlemler
- Event dispatching BaseService'de olmalı

## İsimlendirme Konvansiyonları

### Bu Pattern'leri MUTLAKA Takip Et:
- FormRequest: `{Model}{Action}Request` (PageStoreRequest)
- DTO: `{Model}{Action}DTO` (PageStoreDTO)
- Service: `{Model}Service` (PageService - tüm CRUD için tek service)
- Action: `{Model}{Action}Action` (PageStoreAction)
- Repository: `{Model}Repository`, `{Model}RepositoryInterface`, `{Model}RepositoryCache`
- Resource: `{Model}Resource`, `{Model}Collection`
- Event: `{Model}{Action}` (PageCreated)
- Exception: `{Model}{Type}Exception` (PageNotFoundException)
- Listener: `Invalidate{Model}Cache`

## Model Kuralları

### Database Schema Gereksinimleri
Her tablo MUTLAKA şu kolonları içermeli:
```sql
CREATE TABLE example_table (
    id CHAR(36) PRIMARY KEY,           -- UUID
    created_by CHAR(36) NULL,          -- Audit field
    updated_by CHAR(36) NULL,          -- Audit field
    created_at TIMESTAMP NULL,         -- Laravel timestamp
    updated_at TIMESTAMP NULL,         -- Laravel timestamp
    deleted_at TIMESTAMP NULL,         -- Soft delete
    
    -- Model-specific columns...
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Model Tanımlama Kuralları
- MUTLAKA BaseModel'den extend et
- Table name tanımla
- Sadece model-specific fillable tanımla
- Filtering ve sorting array'lerini tanımla
- Model-specific relationships tanımla
- Gerekirse model-specific casts tanımla
- BaseModel'deki ortak özellikleri tekrar tanımlama

## Caching Kuralları

### MUTLAKA:
- Tüm caching için SADECE Redis kullan (query, session, queue)
- Repository'de Cache Decorator Pattern kullan
- Grup invalidation için cache tag'leri kullan
- Event Listener'larda cache'i invalidate et
- Cache TTL sabitlerini tanımla
- Tutarlı cache key isimlendirmesi kullan

### Cache Key Pattern'i:
```
{resource}.{id}                    // Tek öğe
{resource}.{attribute}.{value}     // Özelliğe göre
{resource}.{filter_name}           // Filtrelenmiş liste
```

### Örnek:
```php
"page.123"
"page.slug.hakkimizda"
"pages.active"
```

## API Response Standartları

### Başarı Response Formatı:
```json
{
    "success": true,
    "data": {},
    "message": "İşlem başarılı",
    "meta": {
        "timestamp": "2025-02-05T10:30:00Z",
        "version": "v1",
        "pagination": {
            "total": 100,
            "per_page": 15,
            "current_page": 1,
            "last_page": 7
        }
    }
}
```

### Hata Response Formatı:
```json
{
    "success": false,
    "message": "Hata mesajı",
    "error_code": "RESOURCE_NOT_FOUND",
    "meta": {
        "timestamp": "2025-02-05T10:30:00Z",
        "version": "v1"
    }
}
```

### MUTLAKA:
- BaseController response metodlarını kullan
- Her response'a meta data ekle
- Doğru HTTP status code'ları kullan (200, 201, 400, 404, 422, 500)
- API Resource'ları ile formatla

## Exception Handling

### MUTLAKA:
- BaseException'dan extend eden özel exception'lar oluştur
- JSON response için BaseException'da render() metodu tanımla
- Spesifik exception'lar kullan (NotFoundException, ValidationException)
- BaseService metodlarında yönet
- Exception'ları context ile logla

**Örnek:**
```php
<?php

declare(strict_types=1);

namespace App\Exceptions\Catalog\Page;

use Exception;
use Illuminate\Http\JsonResponse;

class PageNotFoundException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => 'PAGE_NOT_FOUND',
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ]
        ], 404);
    }
}
```

## Test Gereksinimleri

### Mutlaka Test Yaz:
- Her Action class için (Unit testler)
- Her Service metodu için (Unit testler)
- Her Repository metodu için (Unit testler)
- Her API endpoint için (Feature testler)
- Laravel test araçlarını kullan (PHPUnit/Pest)

## Güvenlik Kuralları

### MUTLAKA:
- Authentication için Sanctum/Passport kullan
- FormRequest'te authorization implement et
- Resource authorization için Policy class'ları kullan
- Tüm input'ları FormRequest'te validate et
- Hassas verileri ASLA loglama
- API route'lara rate limiting uygula
- Production'da HTTPS kullan
- BaseModel'deki audit fields (`created_by`, `updated_by`) otomatik çalışır

## Performans Kuralları

### MUTLAKA:
- Tüm caching için Redis kullan
- N+1 problemini önlemek için eager loading implement et
- Foreign key'ler ve sık sorgulanan kolonlara veritabanı index'leri ekle
- Büyük veri setleri için chunk() kullan
- Repository'de query caching implement et
- Cache Decorator Pattern kullan

## Veritabanı Kuralları

### MUTLAKA:
- Tablo ve kolon isimleri için snake_case kullan
- Her tabloda UUID primary key kullan (BaseModel otomatik)
- Her tabloda `created_by`, `updated_by` audit fields ekle
- Her tabloda `created_at`, `updated_at`, `deleted_at` timestamp'leri ekle
- Migration'larda index'leri ekle
- Foreign key constraint'leri tanımla
- Soft delete support ekle (BaseModel'de mevcut)

## Kod Kalitesi Kuralları

### MUTLAKA:
- Commit'lemeden önce Laravel Pint çalıştır
- PHPStan level 8+ analiz yap
- SOLID prensiplerine uy
- Dependency injection kullan
- Kendini açıklayan kod yaz
- Tüm metodlara DocBlock ekle
- Metodları küçük tut (< 20 satır)
- Kod tekrarından kaçın (DRY - Base class'ları kullan)

## Yapılmaması Gerekenler

### ASLA:
1. Base class'lardan kod kopyalama
2. Controller'larda business logic yazma
3. Service'lerde veritabanı sorguları yazma
4. Action'larda transaction yazma
5. Action'larda event dispatching yazma
6. Gereksiz yere Base metodları override etme
7. Controller'lardan Model'lere direkt erişim
8. Redis dışında cache metodu kullanma
9. BaseRequest'i extend etmeden FormRequest oluşturma
10. BaseService'i extend etmeden Service oluşturma
11. BaseRepository'i extend etmeden Repository oluşturma
12. BaseModel'i extend etmeden Model oluşturma
13. DTO katmanını atlama
14. Cache invalidation için Events/Listeners atlamak
15. Service'lerde response formatlama yapmak
16. Katmanlar arası sorumlulukları karıştırmak
17. Model'de UUID, audit fields, soft deletes'i tekrar tanımlama (BaseModel'de var)

## Kod Üretim Şablonu

Kod üretirken MUTLAKA bu yapıyı takip et:
```php
<?php

declare(strict_types=1);

namespace App\{Layer}\{Module}\{SubModule};

use App\{Layer}\Base{Class};
// Diğer import'lar...

/**
 * {Açıklama}
 */
class {ClassName} extends Base{Class}
{
    public function __construct(
        private readonly DependencyType $dependency,
    ) {}

    /**
     * Metod açıklaması
     *
     * @param ParamType $param
     * @return ReturnType
     */
    public function method(ParamType $param): ReturnType
    {
        // Implementasyon
    }
}
```

## Her Kod Üretiminde Final Checklist

Herhangi bir kod vermeden önce kontrol et:

- [ ] `declare(strict_types=1);` mevcut
- [ ] Modül yapısıyla doğru namespace
- [ ] Uygun Base class'ı extend ediyor
- [ ] Her yerde type hint kullanılmış
- [ ] İsimlendirme konvansiyonlarını takip ediyor
- [ ] Dependency injection kullanıyor
- [ ] DocBlock'lar dahil
- [ ] Base'den kod tekrarı yok
- [ ] Katman sorumlulukları gözetilmiş
- [ ] Türkçe açıklama/yorum, İngilizce kod
- [ ] Veritabanı için snake_case, kod için camelCase
- [ ] Doğru exception handling
- [ ] DTO pattern kullanıyor
- [ ] Yan etkiler için Event/Listener pattern
- [ ] Repository'ler için cache decorator
- [ ] Transaction sadece Service katmanında
- [ ] Model BaseModel'den extend ediyor
- [ ] Model sadece spesifik alanları tanımlıyor (UUID, audit, soft delete BaseModel'de)

---

**Unutma: Base pattern'ler temeldir. Asla iptal etme. %85-98 kod azaltması sağlarlar, tutarlılık getirirler ve enterprise seviye bakım kolaylığı sunarlar. BaseModel tüm ortak model özelliklerini içerir: UUID generation, audit tracking, soft deletes, locale formatting ve filtering/sorting configuration.**