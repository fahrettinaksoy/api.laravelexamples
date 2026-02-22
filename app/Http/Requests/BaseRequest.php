<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\ResponseReference;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    protected function commonRules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $validator->errors()->toArray(),
                'reference' => ResponseReference::build('Doğrulama hatası', 422),
            ], 422),
        );
    }
}
