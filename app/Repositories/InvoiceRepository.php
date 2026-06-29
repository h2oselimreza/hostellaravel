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

    public function getBoarderTemplateDetails(array $boarderAutoIdArr)
    {
        return DB::table('boarder')
            ->select([
                'boarder.boarder_id',
                'boarder.boarder_name',
                'boarder.email',
                'boarder.present_address',
                'boarder.primary_mobile',
                'boarder_invoice_template.item_head',
                'boarder_invoice_template.quantity',
                'boarder_invoice_template.unit_price',
                'item_heads.item_head as item_head_name',
                'item_heads.unit_name',
                'item_heads.item_category',
                'item_categories.category_name',
            ])
            ->join('boarder_invoice_template', 'boarder_invoice_template.boarder', '=', 'boarder.boarder_id')
            ->join('item_heads', 'item_heads.item_head_code', '=', 'boarder_invoice_template.item_head')
            ->join('item_categories', 'item_categories.category_code', '=', 'item_heads.item_category')
            ->whereIn('boarder.id', $boarderAutoIdArr)
            ->where('boarder.is_active', 1)
            ->orderBy('boarder.boarder_id')
            ->get();
    }

    public function doGenerate(
        array $summaryBatchArr,
        array $detailsBatchArr,
        array $lastInvoiceDateUpdateArr
    ) {
        DB::transaction(function () use (
            $summaryBatchArr,
            $detailsBatchArr,
            $lastInvoiceDateUpdateArr
        ) {

            // Batch insert invoice summary
            DB::table('invoice_summary')->insert($summaryBatchArr);

            // Batch insert invoice details
            DB::table('invoice_detail')->insert($detailsBatchArr);

            // Update boarder table
            foreach ($lastInvoiceDateUpdateArr as $boarder) {
                DB::table('boarder')
                    ->where('boarder_id', $boarder['boarder_id'])
                    ->update([
                        'last_invoice_date' => $boarder['last_invoice_date'],
                        'updated_by'        => $boarder['updated_by'],
                        'updated_dt_tm'        => $boarder['updated_dt_tm'],
                    ]);
            }
        });
    }
}