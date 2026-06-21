<?php

namespace App\Http\Controllers\Admin\Seat;

use App\Http\Controllers\Controller;
use App\Models\Admin\SeatType;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SeatTypeController extends Controller
{
    public function index(){
        $data = SeatType::get();
        return view('admin.seat-type.index',compact('data'));
    }

    public function create(){
        return view('admin.seat-type.create-edit');
    }

    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'title' => [
                'required',
                'string',
                'max:100',
                Rule::unique('hst_seat_type', 'title'),
            ],
            'description' => 'nullable|string',
        ]);

        try {

            $seatTypeCode = config('constants.SEAT_TYPE_CODE')
                . $tokenService->getTokenByCode(config('constants.SEAT_TYPE_CODE'));

            $seatType = SeatType::create([
                'seat_type_code' => $seatTypeCode,
                'title'          => $request->title,
                'description'    => $request->description,
                'is_active'      => 1,
            ]);

            return redirect()
                ->route('admin.seat.type.index')
                ->with('success', 'Seat Type has been created successfully.');

        } catch (\Exception $e) {

            Log::error('Seat Type Creation Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create the seat type. Please try again later.');
        }
    }

    public function edit($seatTypeCode){
        $data = SeatType::where('seat_type_code', $seatTypeCode)->first();
        return view('admin.seat-type.create-edit',compact('data'));
    }

    public function update(
        Request $request,
        string $seatTypeCode
    ) {
        $request->validate([
            'title' => [
                'required',
                'string',
                'max:100',
                Rule::unique('hst_seat_type', 'title')
                    ->ignore($seatTypeCode, 'seat_type_code'),
            ],
            'description' => 'nullable|string',
        ]);

        try {

            $seatType = SeatType::where(
                'seat_type_code',
                $seatTypeCode
            )->firstOrFail();

            $seatType->update([
                'title'       => $request->title,
                'description' => $request->description,
            ]);

            return redirect()
                ->route('admin.seat.type.edit', $seatType->seat_type_code)
                ->with('success', 'Seat Type has been updated successfully.');

        } catch (\Exception $e) {

            Log::error('Seat Type Update Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update the seat type. Please try again later.');
        }
    }

    public function checkDuplicateSeatType(Request $request)
    {
        $query = DB::table('hst_seat_type')
            ->where('title', trim($request->title));

        if (!empty($request->seatTypeCode)) {
            $query->where('seat_type_code', '!=', $request->seatTypeCode);
        }

        return response()->json([
            'status' => $query->exists() ? 2 : 1
        ]);
    }
}
