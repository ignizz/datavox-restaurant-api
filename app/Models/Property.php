<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $table = 'properties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'development_id',
        'property_type_id',
        'user_id',
        'record',
        'blocking',
        'name',
    ];


    /**
     * Get the development that owns the property.
     */
    public function development()
    {
        return $this->belongsTo(Development::class);
    }

    /**
     * Get the property type of the property.
     */
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the user who owns the property.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
