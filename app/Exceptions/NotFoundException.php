<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends BaseException
{
    protected int $statusCode = 404;

    protected string $errorCode = 'RESOURCE_NOT_FOUND';
}
