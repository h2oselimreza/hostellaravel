<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Repositories\BoarderRepository;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoarderController extends Controller
{

    public function index( BoarderRepository $boarderRepository )
    {
    
        $boarders = $boarderRepository->getBoarders([
            'isActive' => null,
        ]);

        $seats = $boarderRepository->getVacantSeats();
        
        return view('admin.boarder.index', compact('boarders','seats'));
    }

    public function boarderStatusChange(Request $request)
    {
        $boarderId = $request->boarderId;
        $status = $request->status;
        DB::beginTransaction();

        try {

            if ($status == 2) {

                DB::table('boarder')
                    ->where('boarder_id', $boarderId)
                    ->update([
                        'is_active'   => 0,
                        'updated_by'  => Auth::user()->user_id,
                        'updated_dt_tm' => Carbon::now(),
                    ]);

                $seatAllocation = DB::table('seat_allocation')
                    ->where('boarder', $boarderId)
                    ->first();

                if ($seatAllocation) {

                    DB::table('seat_allocation_log')->insert([
                        'seat'             => $seatAllocation->seat,
                        'boarder'          => $boarderId,
                        'allocated_dt_tm'  => $seatAllocation->allocated_dt_tm,
                        'log_type'         => config('constants.LOG_TYPE_BOARDER_INACTIVE'),
                        'created_by'       => Auth::user()->user_id,
                        'created_dt_tm'    => Carbon::now(),
                        'updated_by'       => Auth::user()->user_id,
                        'updated_dt_tm'    => Carbon::now(),
                    ]);

                    DB::table('seat_allocation')
                        ->where('boarder', $boarderId)
                        ->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Boarder inactive successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function vacantSeat(Request $request)
    {
        $boarderId = $request->boarderId;

        DB::beginTransaction();

        try {

            $seatAllocation = DB::table('seat_allocation')
                ->where('boarder', $boarderId)
                ->first();

            if (!$seatAllocation) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'code' => 2,
                    'message' => 'No seat allocation found.'
                ]);
            }

            DB::table('seat_allocation_log')->insert([
                'seat'            => $seatAllocation->seat,
                'boarder'         => $boarderId,
                'allocated_dt_tm' => $seatAllocation->allocated_dt_tm,
                'log_type'        => config('constants.LOG_TYPE_VACANT'),
                'created_by'      => Auth::user()->user_id,
                'created_dt_tm'   => Carbon::now(),
                'updated_by'      => Auth::user()->user_id,
                'updated_dt_tm'   => Carbon::now(),
            ]);

            DB::table('seat_allocation')
                ->where('boarder', $boarderId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'code' => 1,
                'message' => 'Seat vacated successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function transferSeat(Request $request)
    {
        $boarderId = $request->boarderId;
        $seatCode  = $request->seatCode;

        DB::beginTransaction();

        try {

            // Check seat already booked
            $seatExists = DB::table('seat_allocation')
                ->where('seat', $seatCode)
                ->exists();

            if ($seatExists) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'code'    => 2,
                    'message' => 'Seat already booked.'
                ]);
            }

            // Check boarder current allocation
            $currentAllocation = DB::table('seat_allocation')
                ->where('boarder', $boarderId)
                ->first();

            if (!$currentAllocation) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'code'    => 3,
                    'message' => 'Boarder seat allocation not found.'
                ]);
            }

            // Insert transfer log
            DB::table('seat_allocation_log')->insert([
                'seat'             => $currentAllocation->seat,
                'boarder'          => $boarderId,
                'allocated_dt_tm'  => $currentAllocation->allocated_dt_tm,
                'log_type'         => config('constants.LOG_TYPE_TRANSFER'),
                'created_by'       => Auth::user()->user_id,
                'created_dt_tm'    => Carbon::now(),
                'updated_by'       => Auth::user()->user_id,
                'updated_dt_tm'    => Carbon::now(),
            ]);

            // Delete old allocation
            DB::table('seat_allocation')
                ->where('boarder', $boarderId)
                ->delete();

            // Insert new allocation
            DB::table('seat_allocation')->insert([
                'seat'             => $seatCode,
                'boarder'          => $boarderId,
                'allocated_dt_tm'  => Carbon::now(),
                'created_by'       => Auth::user()->user_id,
                'created_dt_tm'    => Carbon::now(),
                'updated_by'       => Auth::user()->user_id,
                'updated_dt_tm'    => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'code'    => 1,
                'message' => 'Seat transferred successfully.'
            ]);

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
