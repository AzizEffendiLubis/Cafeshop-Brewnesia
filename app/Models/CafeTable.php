<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CafeTable extends Model
{
    protected $table = 'tables';
    protected $fillable = ['number', 'name', 'status'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function activeOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id')
            ->whereNotIn('status', ['done', 'cancelled']);
    }

}
