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

        #[FormField(type: 'text', sort_order: 3)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $slug = null,

        #[FormField(type: 'textarea', sort_order: 4)]
        #[TableColumn(['showing'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $description = null,

        #[FormField(type: 'image', sort_order: 5)]
        #[TableColumn([])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $logo = null,

        #[FormField(type: 'boolean', options: ['false' => 'passive', 'true' => 'active'], sort_order: 6)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_active = null,
    ) {}
}
