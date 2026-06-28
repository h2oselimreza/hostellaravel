<?php

namespace App\Repositories;

use App\Models\Admin\Boarder;
use App\Models\Admin\InvoiceSummary;
use App\Models\Company;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    public function getInvoiceSummary(array $data)
    {
        $query = InvoiceSummary::query();

        if (!empty($data['invoiceType'])) {
            $query->where('invoice_type', $data['invoiceType']);
        }

        if (!empty($data['invoiceNo'])) {
            $query->where('invoice_no', $data['invoiceNo']);
        }

        if (!empty($data['referenceNo'])) {
            $query->where('reference_no', $data['referenceNo']);
        }

        return $query->orderByDesc('created_dt_tm')->get();
    }

    public function getBoarderList()
    {
        return Boarder::select(
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
                'seat_allocation.allocated_dt_tm'
            )
            ->join('seat_allocation', 'seat_allocation.boarder', '=', 'boarder.boarder_id')
            ->join('hst_seat', 'hst_seat.seat_code', '=', 'seat_allocation.seat')
            ->join('hst_seat_type', 'hst_seat_type.seat_type_code', '=', 'hst_seat.seat_type')
            ->join('hst_room', 'hst_room.room_code', '=', 'hst_seat.room')
            ->join('hst_floor', 'hst_floor.floor_code', '=', 'hst_room.floor')
             ->join('hst_building', 'hst_building.building_code', '=', 'hst_floor.building')
            ->where('boarder.is_active', 1)
            ->get();
    }

    public function getInvoiceDetails($invoiceNo)
    {
        return DB::table('invoice_detail')
            ->select(
                'invoice_detail.*',
                'item_heads.item_head as item_head_name'
            )
            ->join(
                'item_heads',
                'item_heads.item_head_code',
                '=',
                'invoice_detail.item_head'
            )
            ->where('invoice_detail.invoice_no', $invoiceNo)
            ->get();
    }

    public function getInvoiceFiles($invoiceNo)
    {
        return DB::table('invoice_files')
            ->where('invoice_no', $invoiceNo)
            ->get();
    }
}