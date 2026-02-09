<?php

declare(strict_types=1);

namespace App\Models\Content\Page;

use App\Models\BaseModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;

class PageModel extends BaseModel
{
    protected $table = 'cnt_page';

    protected $primaryKey = 'page_id';

    public $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'published_at',
    ];

    public array $allowedFiltering = [
        'title',
        'slug',
        'content',
        'excerpt',
        'is_active',
    ];

    public function getAllowedFilters(): array
    {
        return [
            'title',
            'slug',
            'content',
            'excerpt',
            AllowedFilter::exact('is_active'),
            AllowedFilter::scope('published'),
            AllowedFilter::trashed(),
        ];
    }

    public array $allowedSorting = [
        'title',
        'published_at',
        'created_at',
        'updated_at',
    ];

    public array $allowedRelations = [
        'createdBy',
        'updatedBy',
    ];

    public string $defaultSorting = '-created_at';

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
