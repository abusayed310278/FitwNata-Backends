<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $guarded=[''];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the specifications for the product.
     */
    public function productSpecifications()
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count.
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Check if product is in stock.
     */
    public function getInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get the featured image URL.
     */
    // public function getFeaturedImageUrlAttribute()
    // {
    //     if ($this->featured_image) {
    //         return asset('storage/' . $this->featured_image);
    //     }

    //     // Fallback to first image in images array
    //     if (!empty($this->images) && is_array($this->images) && !empty($this->images[0])) {
    //         return asset('storage/' . $this->images[0]);
    //     }

    //     // Default placeholder image
    //     return asset('images/placeholder-product.jpg');
    // }

    protected function images(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Decode JSON or array from DB
                $images = is_array($value) ? $value : json_decode($value ?? '[]', true);

                // If empty, return a single default image
                if (empty($images)) {
                    return [asset('images/default-product.png')];
                }

                // Convert all relative paths to full URLs
                return collect($images)->map(function ($img) {
                    // Already a full URL? Return as-is
                    if (preg_match('/^https?:\/\//', $img)) {
                        return $img;
                    }

                    // Otherwise, prefix with asset()
                    return asset($img);
                })->toArray();
            }
        );
    }

    protected function featuredImage(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // If null or empty, use default avatar
                if (empty($value)) {
                    return asset('assets/images/default-product.png');
                }

                // If already a full URL (starts with http or https), return as-is
                if (preg_match('/^https?:\/\//', $value)) {
                    return $value;
                }

                // Otherwise, it's a relative path from /public, so prefix with asset()
                return asset($value);
            }
        );
    }

    /**
     * Get specifications as an associative array for backward compatibility.
     */
    public function getSpecificationsAttribute()
    {
        // Always load specifications if not already loaded to maintain backward compatibility
        if (!$this->relationLoaded('productSpecifications')) {
            // Use a try-catch block to handle cases where the relationship can't be loaded
            try {
                $this->load('productSpecifications');
            } catch (\Exception $e) {
                // Return empty array if we can't load the relationship
                return [];
            }
        }

        // Check if specifications relationship exists and has data
        if ($this->relationLoaded('productSpecifications')) {
            $specifications = $this->getRelation('productSpecifications');
            if ($specifications) {
                return $specifications->pluck('value', 'key')->toArray();
            }
        }

        // Return empty array as fallback
        return [];
    }

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products by category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    /**
     * Get specifications as an associative array.
     * This is an alias for backward compatibility.
     */
    public function getSpecificationsArrayAttribute()
    {
        return $this->specifications;
    }

    /**
     * Set specifications from an associative array.
     */
    public function setSpecificationsFromArray($specifications)
    {
        // Delete existing specifications
        $this->productSpecifications()->delete();

        // Create new specifications
        if (is_array($specifications)) {
            $specs = [];
            $sortOrder = 0;
            foreach ($specifications as $key => $value) {
                $specs[] = [
                    'product_id' => $this->id,
                    'key' => $key,
                    'value' => $value,
                    'sort_order' => $sortOrder++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($specs)) {
                ProductSpecification::insert($specs);
            }
        }
    }

    /**
     * Generate a unique SKU if not provided.
     */
    public static function generateSKU($name = null)
    {
        $prefix = $name ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3)) : 'PRD';
        $suffix = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $sku = $prefix . $suffix;

        // Ensure uniqueness
        while (static::where('sku', $sku)->exists()) {
            $suffix = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $sku = $prefix . $suffix;
        }

        return $sku;
    }
}
