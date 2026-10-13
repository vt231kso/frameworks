<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courier extends Model
{
    use HasFactory;
    /**
     * Масив fillable
     * @var string[]
     */
    protected $fillable = ['name', 'phone', 'vehicle_type'];

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
