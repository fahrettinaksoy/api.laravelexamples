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
            'fields.array' => __('api.validation.fields_array'),
            'include.string' => __('api.validation.include_string'),
            'sort.string' => __('api.validation.sort_string'),
            'limit.integer' => __('api.validation.limit_integer'),
            'limit.min' => __('api.validation.limit_min'),
            'filter.array' => __('api.validation.filter_array'),
        ];
    }

    public function attributes(): array
    {
        return [];
    }
}
