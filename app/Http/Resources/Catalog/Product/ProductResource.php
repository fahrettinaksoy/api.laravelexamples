<?php

declare(strict_types=1);

namespace App\Http\Resources\Catalog\Product;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ProductResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'cost' => $this->cost ? (float) $this->cost : null,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'category' => $this->whenLoaded('category'),
            'brand' => $this->whenLoaded('brand'),
            'images' => $this->whenLoaded('images'),
            'translations' => $this->whenLoaded('translations'),
            'primary_image' => $this->whenLoaded('primaryImage'),
            'created_by' => $this->whenLoaded('createdBy'),
            'updated_by' => $this->whenLoaded('updatedBy'),
        ];
    }
}
