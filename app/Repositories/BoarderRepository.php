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

    public function getBoarderPersonalInfo(
        $boarderId = null, 
        $boarderIdArr = array(), 
        $flag = null
    ) {
        $query = DB::table('boarder')
            ->select([
                'boarder.*',

                'occupation_father_tb.element as father_occupation_name',
                'occupation_mother_tb.element as mother_occupation_name',
                'occupation_spouse_tb.element as spouse_occupation_name',

                'emer_con_rel_tb.element as emer_contact_relation_name',

                'guardian_rel_tb.element_code as guardian_relation_name',

                'designation_tb.element as designation_name',
            ])

            ->leftJoin(
                'common_table as occupation_father_tb',
                'occupation_father_tb.element_code',
                '=',
                'boarder.father_occupation'
            )

            ->leftJoin(
                'common_table as occupation_mother_tb',
                'occupation_mother_tb.element_code',
                '=',
                'boarder.mother_occupation'
            )

            ->leftJoin(
                'common_table as occupation_spouse_tb',
                'occupation_spouse_tb.element_code',
                '=',
                'boarder.spouse_occupation'
            )

            ->leftJoin(
                'common_table as emer_con_rel_tb',
                'emer_con_rel_tb.element_code',
                '=',
                'boarder.emer_contact_relation'
            )

            ->leftJoin(
                'common_table as guardian_rel_tb',
                'guardian_rel_tb.element_code',
                '=',
                'boarder.guardian_relation'
            )

            ->leftJoin(
                'common_table as designation_tb',
                'designation_tb.element_code',
                '=',
                'boarder.designation'
            )

            ->leftJoin(
                'users',
                'users.user_id',
                '=',
                'boarder.boarder_id'
            )

            ->leftJoin(
                'user_group',
                'user_group.id',
                '=',
                'users.user_group'
            );

        if (is_null($flag)) {
            $query->where('boarder.is_active', 1);
        }

        if ($boarderId) {
            $query->where('boarder.boarder_id', $boarderId);
        } elseif (!empty($boarderIdArr)) {
            $query->whereIn('boarder.boarder_id', $boarderIdArr);
        }

        return $query->get()->toArray();
    }

    public function getInvoiceTemplate($boarderId)
    {
        return DB::table('boarder_invoice_template')
            ->select(
                'boarder_invoice_template.*',
                'item_heads.item_head as item_head_name'
            )
            ->join(
                'item_heads',
                'item_heads.item_head_code',
                '=',
                'boarder_invoice_template.item_head'
            )
            ->where('boarder', $boarderId)
            ->get()
            ->toArray();
    }

    public function getAdmissionHead($headCode)
    {
        return DB::table('item_heads')
            ->select(
                'item_heads.*',
                'item_categories.parent_category_str',
                'item_categories.category_name'
            )
            ->join(
                'item_categories',
                'item_categories.category_code',
                '=',
                'item_heads.item_category'
            )
            ->where('item_heads.item_head_code', $headCode)
            ->get()
            ->toArray();
    }

    public function getAdmissionFee($boarderId)
    {
        return DB::table('invoice_summary')
            ->select('invoice_summary.*')
            ->join(
                'invoice_detail',
                'invoice_detail.invoice_no',
                '=',
                'invoice_summary.invoice_no'
            )
            ->join(
                'item_heads',
                'item_heads.item_head_code',
                '=',
                'invoice_detail.item_head'
            )
            ->where('invoice_detail.item_head', config('constants.ADMISSION_FEE_HEAD_CODE'))
            ->where('invoice_summary.boarder', $boarderId)
            ->where('invoice_summary.is_admission_invoice', 1)
            ->first();
    }
}