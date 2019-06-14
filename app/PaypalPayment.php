<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalPayment extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'payment_id', 'payer_id', 'payment_state', 'price', 'currency', 'approval_link'
    ];

    /**
     * Relation to reservation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
