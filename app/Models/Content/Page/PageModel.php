<?php

declare(strict_types=1);

namespace App\Models\Content\Page;

use App\DataTransferObjects\Content\Page\PageDTO;
use App\Models\BaseModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;

class PageModel extends BaseModel
{
    protected $table = 'cnt_page';

    protected $primaryKey = 'page_id';

    protected static ?string $fieldSource = PageDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'title',
            'slug',
            'content',
            'excerpt',
            AllowedFilter::exact('is_active'),
            AllowedFilter::trashed(),
        ];
    }

    protected array $allowedRelations = [
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = '-created_at';

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];
}
