<?php

namespace App\Http\Controllers\Admin\Seat;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use App\Models\Admin\Floor;
use App\Models\Admin\Room;
use App\Models\Admin\Seat;
use App\Models\Admin\SeatType;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SeatController extends Controller
{
    public function index(){
        $data = Seat::with('seatType','roomInfo.floorInfo.buildingInfo')->where('is_active', 1)->get();
        return view('admin.seat.index',compact('data'));
    }

    public function create(){
        $buildings = Building::where('is_active',1)->get();
        $seatTypes = SeatType::where('is_active',1)->get();
        return view('admin.seat.create-edit', compact('buildings','seatTypes'));
    }

    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_seat', 'title')
                    ->where(fn ($query) => $query->where('room', $request->roomCode)),
            ],
            'seatTypeCode' => 'required|string|max:50',
            'roomCode' => 'required|string|max:50',
            'description' => 'nullable',
        ]);

        try {

            $seatCode = config('constants.SEAT_CODE')
                . $tokenService->getTokenByCode(config('constants.SEAT_CODE'));

            $seat = Seat::create([
                'seat_code'     => $seatCode,
                'seat_type'     => $request->seatTypeCode,
                'room'          => $request->roomCode,
                'title'         => $request->title,
                'description'   => $request->description,
                'is_active'      => 1,
            ]);

            return redirect()->route(
                'admin.seat.edit',[$seat->seat_code]
            )->with('success', 'Seat has been created successfully.');

        } catch (\Exception $e) {
            Log::error('Seat Creation Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create the seat at this time. Please try again later.');
        }
    }

    public function edit($seatCode){
        $data = Seat::with('roomInfo.floorInfo.buildingInfo')->where('seat_code', $seatCode)->first();
        $buildings = Building::where('is_active',1)->get();
        $seatTypes = SeatType::where('is_active',1)->get();
        return view('admin.seat.create-edit',compact('data','buildings','seatTypes'));
    }

    
    public function update(Request $request, string $seatCode)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_seat', 'title')
                    ->where(fn ($query) => $query->where('room', $request->roomCode))
                    ->ignore($seatCode, 'seat_code'),
            ],
            'seatTypeCode' => 'required|string|max:50',
            'roomCode' => 'required|string|max:50',
            'description' => 'nullable',
        ]);

        try {

            $seat = Seat::where('seat_code', $seatCode)->firstOrFail();

            $seat->update([
                'seat_type'     => $request->seatTypeCode,
                'room'          => $request->roomCode,
                'title'       => $request->title,
                'description' => $request->description,
            ]);

            return redirect()
                ->route('admin.seat.edit', [$seat->seat_code])
                ->with('success', 'Seat has been updated successfully.');

        } catch (\Exception $e) {

            Log::error('Seat Update Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update the seat at this time. Please try again later.');
        }
    }


    public function checkDuplicateSeat(Request $request)
    {    
        $exists = DB::table('hst_seat')
            ->join('hst_room', 'hst_room.room_code', '=', 'hst_seat.room')
            ->join('hst_floor', 'hst_floor.floor_code', '=', 'hst_room.floor')
            ->where('hst_seat.title', $request->title)
            ->where('hst_floor.building', $request->buildingCode)
            ->where('hst_room.floor', $request->floorCode)
            ->where('hst_seat.room', $request->roomCode);

            if (!empty($request->seatCode)) {
                $exists->where('hst_seat.seat_code', '!=', $request->seatCode);
            }

        return response()->json([
            'status' => $exists->exists() ? 2 : 1
        ]);
    }

    public function getFloors(Request $request)
    {
        $floors = Floor::where('building', $request->buildingCode)
            ->select('floor_code', 'title')
            ->get();

        return response()->json($floors);
    }

    public function getRooms(Request $request)
    {
        $rooms = Room::where('floor', $request->floorCode)
            ->select('room_code', 'title')
            ->get();

        return response()->json($rooms);
    }
}
