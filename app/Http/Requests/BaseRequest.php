<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    protected function commonRules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute alanı zorunludur.',
            'string' => ':attribute alanı metin olmalıdır.',
            'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
            'unique' => ':attribute zaten kullanılmaktadır.',
            'max' => ':attribute alanı en fazla :max karakter olmalıdır.',
            'min' => ':attribute alanı en az :min karakter olmalıdır.',
            'boolean' => ':attribute alanı true veya false olmalıdır.',
            'integer' => ':attribute alanı tam sayı olmalıdır.',
            'exists' => 'Seçilen :attribute geçersizdir.',
            'in' => 'Seçilen :attribute geçersizdir.',
            'date' => ':attribute geçerli bir tarih olmalıdır.',
            'after' => ':attribute, :date tarihinden sonra olmalıdır.',
            'before' => ':attribute, :date tarihinden önce olmalıdır.',
            'confirmed' => ':attribute onayı eşleşmiyor.',
            'same' => ':attribute ile :other eşleşmelidir.',
            'different' => ':attribute ile :other farklı olmalıdır.',
            'numeric' => ':attribute sayısal olmalıdır.',
            'array' => ':attribute dizi olmalıdır.',
            'url' => ':attribute geçerli bir URL olmalıdır.',
            'image' => ':attribute resim dosyası olmalıdır.',
            'mimes' => ':attribute dosya tipi :values olmalıdır.',
            'size' => ':attribute boyutu :size olmalıdır.',
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active' => 'Aktif',
            'created_at' => 'Oluşturulma Tarihi',
            'updated_at' => 'Güncellenme Tarihi',
            'sort' => 'Sıralama',
            'per_page' => 'Sayfa Başına Kayıt',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'version' => 'v1',
                ],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
