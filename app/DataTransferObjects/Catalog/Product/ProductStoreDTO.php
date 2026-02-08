<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Catalog\Product;

use Illuminate\Http\Request;

readonly class ProductStoreDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $sku,
        public ?string $description,
        public ?string $short_description,
        public float $price,
        public ?float $sale_price,
        public ?float $cost,
        public int $stock,
        public ?string $category_id,
        public ?string $brand_id,
        public bool $is_active,
        public bool $is_featured,
        public ?string $meta_title,
        public ?string $meta_description,
        public ?string $meta_keywords,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            slug: $request->input('slug'),
            sku: $request->input('sku'),
            description: $request->input('description'),
            short_description: $request->input('short_description'),
            price: (float) $request->input('price'),
            sale_price: $request->input('sale_price') ? (float) $request->input('sale_price') : null,
            cost: $request->input('cost') ? (float) $request->input('cost') : null,
            stock: (int) $request->input('stock', 0),
            category_id: $request->input('category_id'),
            brand_id: $request->input('brand_id'),
            is_active: $request->boolean('is_active', true),
            is_featured: $request->boolean('is_featured', false),
            meta_title: $request->input('meta_title'),
            meta_description: $request->input('meta_description'),
            meta_keywords: $request->input('meta_keywords'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'cost' => $this->cost,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
        ];
    }
}
