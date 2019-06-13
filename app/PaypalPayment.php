<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalPayment extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'payment_id', 'payer_id', 'payment_status'
    ];

    /**
     * Relation to reservation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservations()
    {
        return $this->belongsTo(Reservation::class);
    }
}
