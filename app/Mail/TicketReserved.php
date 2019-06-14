<?php

namespace App\Mail;

use App\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketReserved extends Mailable
{
    use Queueable, SerializesModels;

    private $reservation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = "Your reservation is approved.</br>
        Your invoice total: {$this->reservation->price} {$this->reservation->currency}</br>
        Your seats: </br>";
        $seats = $this->reservation->seats()->get();
        foreach ($seats as $seat) {
            $email .= "Row number: {$seat->row_number}, seat number: {$seat->seat_number}</br>";
        }
        return $this->from('info@rafmovies.com')->html($email);
    }
}
