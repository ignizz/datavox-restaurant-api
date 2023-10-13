<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDetail extends Model
{
    use HasFactory;

    protected $table = 'property_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'measurements',
        'land',
        'built',
        'surface',
    ];

    /**
     * Get the property that owns the property detail.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
