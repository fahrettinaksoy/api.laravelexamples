<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BaseUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), []);
    }

    public function messages(): array
    {
        return [];
    }
}
