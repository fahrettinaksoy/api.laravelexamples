<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Content\Page;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

class PageDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show', 'destroy'])]
        public readonly ?int $page_id = null,

        #[FormField(type: 'text', sort_order: 2)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $title = null,

        #[FormField(type: 'text', sort_order: 3)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?string $slug = null,

        #[FormField(type: 'richtext', sort_order: 4)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['show', 'store', 'update'])]
        public readonly ?string $content = null,

        #[FormField(type: 'textarea', sort_order: 5)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update'])]
        public readonly ?string $excerpt = null,

        #[FormField(type: 'text', sort_order: 6)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_title = null,

        #[FormField(type: 'textarea', sort_order: 7)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_description = null,

        #[FormField(type: 'text', sort_order: 8)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $meta_keywords = null,

        #[FormField(type: 'boolean', options: ['false' => 'passive', 'true' => 'active'], sort_order: 9)]
        #[TableColumn(['showing', 'filtering'])]
        #[ActionType(['index', 'show', 'store', 'update', 'destroy'])]
        public readonly ?bool $is_active = null,

        #[FormField(type: 'datetime', sort_order: 10)]
        #[TableColumn(['showing', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update'])]
        public readonly ?string $published_at = null,
    ) {}
}
