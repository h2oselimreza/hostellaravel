<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use App\Models\Admin\Floor;
use App\Models\Admin\Room;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(){
        $data = Room::with('floorInfo','floorInfo.buildingInfo')->get();
        return view('admin.room.index',compact('data'));
    }

    public function create(){
        $buildings = Building::where('is_active',1)->get();
        return view('admin.room.create-edit', compact('buildings'));
    }

    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_room', 'title')
                    ->where(fn ($query) => $query->where('floor', $request->floorCode)),
            ],
            'floorCode' => 'required|string|max:50',
            'description' => 'nullable',
        ]);

        try {

            $roomCode = config('constants.ROOM_CODE')
                . $tokenService->getTokenByCode(config('constants.ROOM_CODE'));

            $room = Room::create([
                'room_code'     => $roomCode,
                'floor'       => $request->floorCode,
                'title'          => $request->title,
                'description'          => $request->description,
                'is_active'      => 1,
            ]);

            return redirect()->route(
                'admin.room.edit',[$room->room_code]
            )->with('success', 'Room has been created successfully.');

        } catch (\Exception $e) {
            Log::error('Room Creation Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create the room at this time. Please try again later.');
        }
    }

    public function edit($roomCode){
        $data = Room::with('floorInfo','floorInfo.buildingInfo')->where('room_code', $roomCode)->first();
        $buildings = Building::where('is_active',1)->get();
        return view('admin.room.create-edit',compact('data','buildings'));
    }

    
    public function update(Request $request, string $roomCode)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_room', 'title')
                    ->where(fn ($query) => $query->where('floor', $request->floorCode))
                    ->ignore($roomCode, 'room_code'),
            ],
            'floorCode'    => 'required|string|max:50',
            'description'  => 'nullable',
        ]);

        try {

            $room = Room::where('room_code', $roomCode)->firstOrFail();

            $room->update([
                'floor'       => $request->floorCode,
                'title'       => $request->title,
                'description' => $request->description,
            ]);

            return redirect()
                ->route('admin.room.edit', [$room->room_code])
                ->with('success', 'Room has been updated successfully.');

        } catch (\Exception $e) {

            Log::error('Room Update Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update the room at this time. Please try again later.');
        }
    }


    public function checkDuplicateRoom(Request $request)
    {
        $query = DB::table('hst_room')
            ->join('hst_floor', 'hst_floor.floor_code', '=', 'hst_room.floor')
            ->where('hst_room.title', $request->title)
            ->where('hst_floor.building', $request->buildingCode)
            ->where('hst_room.floor', $request->floorCode);

        // Update current room ignore
        if (!empty($request->roomCode)) {
            $query->where('hst_room.room_code', '!=', $request->roomCode);
        }

        return response()->json([
            'status' => $query->exists() ? 2 : 1
        ]);
    }

    public function getFloors(Request $request)
    {
        $floors = Floor::where('building', $request->buildingCode)
            ->select('floor_code', 'title')
            ->get();

        return response()->json($floors);
    }
}
