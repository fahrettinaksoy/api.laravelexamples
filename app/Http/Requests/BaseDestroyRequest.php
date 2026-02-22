<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BaseDestroyRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'ids' => 'sometimes',
            'ids.*' => 'integer|min:1',
        ]);
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }
}
