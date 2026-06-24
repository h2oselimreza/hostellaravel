<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoarderRepository
{
    public function getRooms(
        ?string $roomCode = null,
        array $roomCodeArr = [],
        int $flag = 2
    ) {
        $query = DB::table('hst_room')
            ->select([
                'hst_room.*',
                'hst_building.title as building_title',
                'hst_building.building_code',
                'hst_floor.title as floor_title',
                'hst_floor.floor_code',

                DB::raw("
                    (
                        SELECT COUNT(*)
                        FROM hst_seat
                        WHERE hst_seat.room = hst_room.room_code
                    ) as total_seat_count
                "),

                DB::raw('COALESCE(allocated_seat_count_view.allocated_seat_count, 0) as allocated_seat_count'),
            ])
            ->join('hst_floor', 'hst_floor.floor_code', '=', 'hst_room.floor')
            ->join('hst_building', 'hst_building.building_code', '=', 'hst_floor.building')
            ->leftJoin(
                'allocated_seat_count_view',
                'allocated_seat_count_view.room',
                '=',
                'hst_room.room_code'
            );

        if ($flag === 1) {
            $query->where('hst_room.is_active', 1);
        } elseif ($flag === 0) {
            $query->where('hst_room.is_active', 0);
        }

        if (!empty($roomCode)) {
            $query->where('hst_room.room_code', $roomCode);
        } elseif (!empty($roomCodeArr)) {
            $query->whereIn('hst_room.room_code', $roomCodeArr);
        }

        return $query->get();
    }

    public function getRoomSeats(string $roomCode)
    {
        return DB::table('hst_seat')
            ->select([
                'hst_seat.*',

                'hst_seat_type.title as seat_type_title',

                'hst_building.title as building_title',
                'hst_building.building_code',

                'hst_floor.title as floor_title',
                'hst_floor.floor_code',

                'hst_room.title as room_title',
                'hst_room.room_code',

                'seat_allocation.boarder',
                'seat_allocation.allocated_dt_tm',

                'boarder.boarder_name',
                'boarder.boarder_id',
            ])
            ->join(
                'hst_seat_type',
                'hst_seat_type.seat_type_code',
                '=',
                'hst_seat.seat_type'
            )
            ->join(
                'hst_room',
                'hst_room.room_code',
                '=',
                'hst_seat.room'
            )
            ->join(
                'hst_floor',
                'hst_floor.floor_code',
                '=',
                'hst_room.floor'
            )
            ->join(
                'hst_building',
                'hst_building.building_code',
                '=',
                'hst_floor.building'
            )
            ->leftJoin(
                'seat_allocation',
                'seat_allocation.seat',
                '=',
                'hst_seat.seat_code'
            )
            ->leftJoin(
                'boarder',
                'boarder.boarder_id',
                '=',
                'seat_allocation.boarder'
            )
            ->where('hst_seat.is_active', 1)
            ->where('hst_seat.room', $roomCode)
            ->get();
    }

    public function getBoarders(array $filters = [])
    {
        $query = DB::table('boarder')
            ->select([
                'boarder.*',

                'hst_building.title as building_title',
                'hst_building.building_code',

                'hst_floor.title as floor_title',
                'hst_floor.floor_code',

                'hst_room.title as room_title',
                'hst_room.room_code',

                'hst_seat.title as seat_title',
                'hst_seat.seat_code',

                'hst_seat_type.title as seat_type_title',

                'seat_allocation.allocated_dt_tm',
            ])
            ->leftJoin(
                'seat_allocation',
                'seat_allocation.boarder',
                '=',
                'boarder.boarder_id'
            )
            ->leftJoin(
                'hst_seat',
                'hst_seat.seat_code',
                '=',
                'seat_allocation.seat'
            )
            ->leftJoin(
                'hst_seat_type',
                'hst_seat_type.seat_type_code',
                '=',
                'hst_seat.seat_type'
            )
            ->leftJoin(
                'hst_room',
                'hst_room.room_code',
                '=',
                'hst_seat.room'
            )
            ->leftJoin(
                'hst_floor',
                'hst_floor.floor_code',
                '=',
                'hst_room.floor'
            )
            ->leftJoin(
                'hst_building',
                'hst_building.building_code',
                '=',
                'hst_floor.building'
            );

        if (($filters['isActive'] ?? null) === 1) {
            $query->where('boarder.is_active', 1);
        }

        if (($filters['isActive'] ?? null) === 2) {
            $query->where('boarder.is_active', 0);
        }

        return $query->get();
    }

    public function getVacantSeats()
    {
        return DB::table('hst_seat')
            ->select([
                'hst_seat.*',

                'hst_seat_type.title as seat_type_title',

                'hst_building.title as building_title',
                'hst_building.building_code',

                'hst_floor.title as floor_title',
                'hst_floor.floor_code',

                'hst_room.title as room_title',
                'hst_room.room_code',

                'seat_allocation.boarder',
                'seat_allocation.allocated_dt_tm',

                'boarder.boarder_name',
                'boarder.boarder_id',
            ])
            ->join(
                'hst_seat_type',
                'hst_seat_type.seat_type_code',
                '=',
                'hst_seat.seat_type'
            )
            ->join(
                'hst_room',
                'hst_room.room_code',
                '=',
                'hst_seat.room'
            )
            ->join(
                'hst_floor',
                'hst_floor.floor_code',
                '=',
                'hst_room.floor'
            )
            ->join(
                'hst_building',
                'hst_building.building_code',
                '=',
                'hst_floor.building'
            )
            ->leftJoin(
                'seat_allocation',
                'seat_allocation.seat',
                '=',
                'hst_seat.seat_code'
            )
            ->leftJoin(
                'boarder',
                'boarder.boarder_id',
                '=',
                'seat_allocation.boarder'
            )
            ->where('hst_seat.is_active', 1)
            ->get();
    }
}