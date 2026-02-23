<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class BaseFieldUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'field' => ['required', 'string', Rule::in($this->fillableFields())],
            'value' => ['present'],
        ]);
    }

    public function messages(): array
    {
        return [
            'field.required' => __('api.validation.field_update.field_required'),
            'field.string' => __('api.validation.field_update.field_string'),
            'field.in' => __('api.validation.field_update.field_not_updatable'),
            'value.present' => __('api.validation.field_update.value_present'),
        ];
    }

    protected function fillableFields(): array
    {
        $modelClass = $this->attributes->get('modelClass');

        if ($modelClass === null) {
            throw new \RuntimeException(
                __('api.controller.model_not_resolved'),
            );
        }

        return app($modelClass)->getFillable();
    }
}
