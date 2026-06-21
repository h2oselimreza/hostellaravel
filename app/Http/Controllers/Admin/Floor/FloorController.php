<?php

namespace App\Http\Controllers\Admin\Floor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use App\Models\Admin\Floor;
use App\Services\TokenService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FloorController extends Controller
{
    public function index(){
        $data = Floor::with('buildingInfo')->get();
        return view('admin.floor.index',compact('data'));
    }

    public function create(){
        $buildings = Building::where('is_active',1)->get();
        return view('admin.floor.create-edit', compact('buildings'));
    }

    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_floor')
                    ->where(fn ($query) => $query->where('building', $request->buildingCode))
                    ->ignore($request->floorCode, 'floor_code'),
            ],
            'buildingCode' => 'required|string|max:50',
        ]);

        try {

            $floorCode = config('constants.FLOOR_CODE')
                . $tokenService->getTokenByCode(config('constants.FLOOR_CODE'));

            $floor = Floor::create([
                'floor_code'     => $floorCode,
                'building'       => $request->buildingCode,
                'title'          => $request->title,
                'is_active'      => 1,
            ]);

            return redirect()->route(
                'admin.floor.edit',[$floor->floor_code]
            )->with('success', 'Floor has been created successfully.');

        } catch (\Exception $e) {
            Log::error('Floor Creation Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create the floor at this time. Please try again later.');
        }
    }

    public function edit($floorCode){
        $data = Floor::where('floor_code', $floorCode)->first();
        $buildings = Building::where('is_active',1)->get();
        return view('admin.floor.create-edit',compact('data','buildings'));
    }

    public function update(Request $request, string $floorCode)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('hst_floor', 'title')
                    ->where(fn ($query) => $query->where('building', $request->buildingCode))
                    ->ignore($floorCode, 'floor_code'),
            ],
            'buildingCode' => 'required|string|max:50',
        ]);

        try {

            $floor = Floor::where('floor_code', $floorCode)->firstOrFail();

            $floor->update([
                'building' => $request->buildingCode,
                'title'    => $request->title,
            ]);

            return redirect()
                ->route('admin.floor.edit', [$floor->floor_code])
                ->with('success', 'Floor has been updated successfully.');

        } catch (\Exception $e) {

            Log::error('Floor Update Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update the floor at this time. Please try again later.');
        }
    }


    public function checkDuplicateFloor(Request $request)
    {
        $exists = Floor::where('title', $request->title)
            ->where('building', $request->buildingCode)
            ->where('floor_code', '!=', $request->floorCode)
            ->exists();

        return response()->json([
            'status' => $exists ? 2 : 1
        ]);
    }
}
