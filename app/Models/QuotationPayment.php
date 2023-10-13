<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quotation_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quotation_id',
        'amount',
        'date',
        'payment_method',
        'voucher',
    ];


    /**
     * Get the quotation associated with the payment.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
