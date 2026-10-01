<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'vehicle_type'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
