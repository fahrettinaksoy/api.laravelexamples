<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Catalog\Product\Subs;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

class ProductTranslationDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $product_translation_id = null,

        #[FormField(type: 'select', sort_order: 2, relationship: ['model' => 'ProductModel', 'label' => 'name'])]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?int $product_id = null,

        #[FormField(type: 'select', sort_order: 3, options: ['tr' => 'Turkce', 'en' => 'English'])]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $locale = null,

        #[FormField(type: 'text', sort_order: 4)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $name = null,

        #[FormField(type: 'textarea', sort_order: 5)]
        #[TableColumn(['showing'])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $description = null,

        #[FormField(type: 'textarea', sort_order: 6)]
        #[TableColumn(['showing'])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $short_description = null,

        #[FormField(type: 'text', sort_order: 7)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_title = null,

        #[FormField(type: 'textarea', sort_order: 8)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_description = null,

        #[FormField(type: 'text', sort_order: 9)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_keywords = null,
    ) {}
}
