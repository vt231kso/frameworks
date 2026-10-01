<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'courier_id', 'product_id', 'delivery_address', 'total_amount', 'status'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
