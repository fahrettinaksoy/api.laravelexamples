<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BaseDestroyRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|min:1',
        ]);
    }

    public function messages(): array
    {
        return [
            'ids.array' => __('api.validation.ids_must_be_array'),
            'ids.min' => __('api.validation.ids_min_one'),
            'ids.*.required' => __('api.validation.id_required'),
            'ids.*.integer' => __('api.validation.id_integer'),
            'ids.*.min' => __('api.validation.id_min_one'),
        ];
    }

    public function attributes(): array
    {
        return [];
    }
}
