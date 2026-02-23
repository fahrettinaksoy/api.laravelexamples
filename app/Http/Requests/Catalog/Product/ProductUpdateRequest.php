<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog\Product;

use App\Http\Requests\BaseUpdateRequest;
use App\Models\Catalog\Product\ProductModel;

class ProductUpdateRequest extends BaseUpdateRequest
{
    public function rules(): array
    {
        $productId = $this->route('id');
        $model = new ProductModel;
        $conn = $model->getConnectionName();
        $table = $model->getTable();
        $pk = $model->getKeyName();

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', "unique:{$conn}.{$table},slug,{$productId},{$pk}"],
            'sku' => ['required', 'string', 'max:100', "unique:{$conn}.{$table},sku,{$productId},{$pk}"],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', "exists:{$conn}.cat_category,category_id"],
            'brand_id' => ['nullable', 'integer', "exists:{$conn}.cat_brand,brand_id"],
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
            'name.required' => __('api.product.name_required'),
            'name.string' => __('api.product.name_string'),
            'name.max' => __('api.product.name_max'),
            'slug.required' => __('api.product.slug_required'),
            'slug.string' => __('api.product.slug_string'),
            'slug.max' => __('api.product.slug_max'),
            'slug.unique' => __('api.product.slug_unique'),
            'sku.required' => __('api.product.sku_required'),
            'sku.string' => __('api.product.sku_string'),
            'sku.max' => __('api.product.sku_max'),
            'sku.unique' => __('api.product.sku_unique'),
            'short_description.max' => __('api.product.short_description_max'),
            'price.required' => __('api.product.price_required'),
            'price.numeric' => __('api.product.price_numeric'),
            'price.min' => __('api.product.price_min'),
            'sale_price.numeric' => __('api.product.sale_price_numeric'),
            'sale_price.min' => __('api.product.sale_price_min'),
            'sale_price.lt' => __('api.product.sale_price_lt'),
            'cost.numeric' => __('api.product.cost_numeric'),
            'cost.min' => __('api.product.cost_min'),
            'stock.required' => __('api.product.stock_required'),
            'stock.integer' => __('api.product.stock_integer'),
            'stock.min' => __('api.product.stock_min'),
            'category_id.integer' => __('api.product.category_id_integer'),
            'category_id.exists' => __('api.product.category_id_exists'),
            'brand_id.integer' => __('api.product.brand_id_integer'),
            'brand_id.exists' => __('api.product.brand_id_exists'),
            'is_active.boolean' => __('api.product.is_active_boolean'),
            'is_featured.boolean' => __('api.product.is_featured_boolean'),
            'meta_title.max' => __('api.product.meta_title_max'),
            'meta_description.max' => __('api.product.meta_description_max'),
            'meta_keywords.max' => __('api.product.meta_keywords_max'),
        ]);
    }
}
