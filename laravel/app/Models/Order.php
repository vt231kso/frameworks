<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    /**
     * Масив fillable
     * @var string[]
     */
    protected $fillable = ['client_id', 'courier_id', 'product_id', 'delivery_address', 'total_amount', 'status'];

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
