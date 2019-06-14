<?php


namespace App\Services\Reservation;


interface ReservationServiceInterface
{
    public function getSeatsMatrix(int $projectionId): array;

    public function reserve($data);

    public function verifyReservation($data);

}
