<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public const PENDING_STATUS = 1;
    public const CANCELED_STATUS = 2;
    public const VERIFIED_STATUS = 3;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'projection_id', 'price', 'status'
    ];

    /**
     * Relation to reserved seats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Relation to PayPal payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function paypalPayment()
    {
        return $this->hasOne(PaypalPayment::class);
    }
}
