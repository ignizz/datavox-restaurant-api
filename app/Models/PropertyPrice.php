<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPrice extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'property_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'amount',
        'type_price',
    ];

    /**
     * @author Gerardo Mata
     * @create 2023-05-30
     * Constants for property price model
     * @var array<uknown>, string
     */
    const PRICE_ORIGINAL = 1, PRICE_DISCOUNT = 2;
    const PRICE_TYPES = [
        self::PRICE_ORIGINAL,
        self::PRICE_DISCOUNT
    ];

    /**
     * Get the property associated with the price.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
