<?php


namespace App\Repositories\Seat;


use App\Repositories\RepositoryInterface;
use App\Seat;
use Illuminate\Support\Collection;

interface SeatRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $projectionId
     * @return Collection|\Illuminate\Database\Eloquent\Collection|Seat[]
     */
    public function getReservedSeats(int $projectionId): Collection;
}
