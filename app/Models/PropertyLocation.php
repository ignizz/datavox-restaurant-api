<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyLocation extends Model
{
    use HasFactory;

    protected $table = 'property_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'coordinates',
        'northern_adjoining',
        'southern_adjoining',
        'manzana',
        'lote',
        'northeast',
        'northwest',
        'southeast',
        'southwest',
    ];

    /**
     * Get the property that owns the location.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
