<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'row_number', 'seat_number'
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
