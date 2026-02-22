<?php

declare(strict_types=1);

namespace App\Models\User;

use App\DataTransferObjects\User\UserDTO;
use App\Traits\HasFieldMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use HasFactory;
    use HasFieldMetadata;
    use Notifiable;

    protected static ?string $fieldSource = UserDTO::class;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
