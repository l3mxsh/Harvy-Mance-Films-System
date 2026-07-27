<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function inventory(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'addon_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
