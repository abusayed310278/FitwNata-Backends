<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $fillable = [
        'product_id',
        'key',
        'value',
        'sort_order'
    ];

    /**
     * Get the product that owns this specification.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
