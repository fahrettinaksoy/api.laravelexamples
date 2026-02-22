<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Catalog\Product;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

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

        #[FormField(type: 'text', sort_order: 3)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $slug = null,

        #[FormField(type: 'text', sort_order: 4)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $sku = null,

        #[FormField(type: 'textarea', sort_order: 5)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $description = null,

        #[FormField(type: 'textarea', sort_order: 6)]
        #[TableColumn(['showing'])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $short_description = null,

        #[FormField(type: 'number', sort_order: 7)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?float $price = null,

        #[FormField(type: 'number', sort_order: 8)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?float $sale_price = null,

        #[FormField(type: 'number', sort_order: 9)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?float $cost = null,

        #[FormField(type: 'number', sort_order: 10)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $stock = null,

        #[FormField(type: 'select', sort_order: 11, relationship: ['model' => 'CategoryModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $category_id = null,

        #[FormField(type: 'select', sort_order: 12, relationship: ['model' => 'BrandModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $brand_id = null,

        #[FormField(type: 'boolean', options: ['false' => 'passive', 'true' => 'active'], sort_order: 13)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_active = null,

        #[FormField(type: 'boolean', options: ['false' => 'no', 'true' => 'yes'], sort_order: 14)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_featured = null,

        #[FormField(type: 'text', sort_order: 15)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_title = null,

        #[FormField(type: 'textarea', sort_order: 16)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_description = null,

        #[FormField(type: 'text', sort_order: 17)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_keywords = null,
    ) {}
}
