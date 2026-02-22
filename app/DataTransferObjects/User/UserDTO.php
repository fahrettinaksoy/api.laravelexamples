<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use App\DataTransferObjects\BaseDTO;

class UserDTO extends BaseDTO
{
    public function __construct(
        #[FormField(type: 'number', sort_order: 1)]
        #[TableColumn(['showing', 'filtering', 'sorting'], ['desc'])]
        #[ActionType(['index', 'show'])]
        public readonly ?int $id = null,

        #[FormField(type: 'text', sort_order: 2)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update'])]
        public readonly ?string $name = null,

        #[FormField(type: 'email', sort_order: 3)]
        #[TableColumn(['showing', 'filtering', 'sorting'])]
        #[ActionType(['index', 'show', 'store', 'update'])]
        public readonly ?string $email = null,

        #[FormField(type: 'password', sort_order: 4)]
        #[TableColumn([])]
        #[ActionType(['store', 'update'])]
        public readonly ?string $password = null,
    ) {}
}
