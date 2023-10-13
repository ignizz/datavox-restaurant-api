<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAddress extends Model
{
    use HasFactory;

    protected $table = 'property_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'street',
        'num_ext',
        'num_int',
        'neighborhood',
        'zip_code',
        'city',
        'state',
    ];

    /**
     * Get the property that owns the address.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
