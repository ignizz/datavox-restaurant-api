<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quotation_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quotation_id',
        'amount',
        'down_payment',
        'down_payment_percentage',
        'discount',
        'payment_number',
        'payment_amount',
        'payment_plan',
        'payment_method',
        'date_start',
        'date_end',
    ];

    /**
     * Get the quotation associated with the quotation detail.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
