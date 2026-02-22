<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BaseIndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'fields' => ['nullable', 'array'],
            'include' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'filter' => ['nullable', 'array'],
        ]);
    }

    public function messages(): array
    {
        return [
            'fields.array' => 'Alanlar bir dizi olmalıdır.',
            'include.string' => 'Dahil edilecek veriler metin olmalıdır.',
            'sort.string' => 'Sıralama bilgisi metin olmalıdır.',
            'limit.integer' => 'Limit bir tamsayı olmalıdır.',
            'limit.min' => 'Limit en az 1 olmalıdır.',
        ];
    }

    public function attributes(): array
    {
        return [];
    }
}
