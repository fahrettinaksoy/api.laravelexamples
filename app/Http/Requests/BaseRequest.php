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
        $message = __('api.validation.error');

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $validator->errors()->toArray(),
                'reference' => app(ResponseReference::class)->build($message, 422),
            ], 422),
        );
    }
}
