<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'image', 'emoji', 'is_available', 'sort_order'
    ];

    protected $casts = ['is_available' => 'boolean', 'price' => 'integer'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('uploads/menus/' . $this->image))) {
            return asset('uploads/menus/' . $this->image);
        }
        return '';
    }
}
