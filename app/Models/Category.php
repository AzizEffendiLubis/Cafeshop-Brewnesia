<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'sort_order', 'is_active'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function availableMenus(): HasMany
    {
        return $this->hasMany(Menu::class)->where('is_available', true);
    }
}
