<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog\Product;

use App\Http\Requests\BaseUpdateRequest;

class ProductUpdateRequest extends BaseUpdateRequest
{
    public function rules(): array
    {
        $productId = $this->route('id');

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', "unique:conn_mysql.cat_product,slug,{$productId},product_id"],
            'sku' => ['required', 'string', 'max:100', "unique:conn_mysql.cat_product,sku,{$productId},product_id"],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:conn_mysql.cat_category,category_id'],
            'brand_id' => ['nullable', 'integer', 'exists:conn_mysql.cat_brand,brand_id'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Ürün adı zorunludur',
            'slug.unique' => 'Bu slug zaten kullanılıyor',
            'sku.required' => 'SKU zorunludur',
            'sku.unique' => 'Bu SKU zaten kullanılıyor',
            'price.required' => 'Fiyat zorunludur',
            'sale_price.lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır',
            'stock.required' => 'Stok miktarı zorunludur',
        ]);
    }
}
