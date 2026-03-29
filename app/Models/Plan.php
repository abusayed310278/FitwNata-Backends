<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'interval',
        'type',
        'stripe_plan_id',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
        'image_url',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    /**
     * Get subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if this is a free plan.
     */
    // public function isFree(): bool
    // {
    //     return $this->price == 0;
    // }

    public function isFree(): bool
    {
        return $this->price == 0 || empty($this->stripe_plan_id);
    }

    /**
     * Check if this is a premium plan.
     */
    public function isPremium(): bool
    {
        return $this->price > 0;
    }

    /**
     * Get plan type based on price.
     */
    public function getTypeAttribute(): string
    {
        return $this->isFree() ? 'free' : 'premium';
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->isFree() ? 'Free' : '$' . number_format($this->price, 2);
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular plans.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

     public function articles()
    {
        return $this->morphedByMany(Article::class, 'planable');
    }

    public function workouts()
    {
        return $this->morphedByMany(Workout::class, 'planable');
    }
}
