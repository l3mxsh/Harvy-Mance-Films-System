<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
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

    public function services(): HasMany
    {
        return $this->hasMany(PackageService::class)->orderBy('sort_order');
    }

    public function inventory(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'package_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
