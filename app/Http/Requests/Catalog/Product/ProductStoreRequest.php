<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'brand_id' => ['nullable', 'string', 'exists:brands,id'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ürün adı zorunludur',
            'slug.unique' => 'Bu slug zaten kullanılıyor',
            'sku.required' => 'SKU zorunludur',
            'sku.unique' => 'Bu SKU zaten kullanılıyor',
            'price.required' => 'Fiyat zorunludur',
            'sale_price.lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır',
            'stock.required' => 'Stok miktarı zorunludur',
        ];
    }
}
