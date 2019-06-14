<?php


namespace App\Repositories\Seat;


use App\Repositories\Repository;
use App\Seat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SeatRepository extends Repository implements SeatRepositoryInterface
{

    /**
     * SeatRepository constructor.
     * @param Seat $seat
     */
    public function __construct(Seat $seat)
    {
        parent::__construct($seat);
    }

    /**
     * @param int $projectionId
     * @return Collection|\Illuminate\Database\Eloquent\Collection|Seat[]
     */
    public function getReservedSeats(int $projectionId): Collection
    {
        return Seat::query()->whereHas('reservation', function ($query) use ($projectionId) {
            $query->where('projection_id', $projectionId);
        })->get();
    }
}
