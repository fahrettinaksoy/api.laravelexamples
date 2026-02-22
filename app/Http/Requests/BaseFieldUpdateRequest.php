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
            'field.required' => 'Alan adı zorunludur',
            'field.string' => 'Alan adı metin olmalıdır',
            'field.in' => "':input' alanı güncellenemez",
            'value.present' => 'Değer alanı gönderilmelidir',
        ];
    }

    protected function fillableFields(): array
    {
        $modelClass = $this->attributes->get('modelClass');

        if ($modelClass === null) {
            return [];
        }

        return app($modelClass)->getFillable();
    }
}
