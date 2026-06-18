<?php

namespace App\Http\Controllers\Admin\Building;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    public function index(){
        $data = Building::get();
        return view('admin.building.index',compact('data'));
    }

    public function create(){
        return view('admin.building.create-edit');
    }

    public function store(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'title'   => 'required|string|max:250',
            'address' => 'nullable|string',
        ]);

        try {

            $buildingCode = config('constants.BUILDING_CODE')
                . $tokenService->getTokenByCode(config('constants.BUILDING_CODE'));

            Building::create([
                'building_code' => $buildingCode,
                'title'         => $request->title,
                'address'       => $request->address,
            ]);

            return redirect()
                ->route('admin.building.index')
                ->with('success', 'Building has been created successfully.');

        } catch (\Exception $e) {

            Log::error('Building Creation Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to create the building at this time. Please try again later.');
        }
    }

    public function edit($buildingCode){
        $data = Building::where('building_code', $buildingCode)->first();
        return view('admin.building.create-edit',compact('data'));
    }

    public function update(Request $request, string $buildingCode)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:250'],
            'address' => ['nullable', 'string'],
        ]);

        try {
            DB::transaction(function () use ($buildingCode, $validated) {

                $building = Building::where('building_code', $buildingCode)
                    ->firstOrFail();

                $building->update([
                    'title'   => trim($validated['title']),
                    'address' => $validated['address'] ?? null,
                ]);
            });

            return redirect()
                ->route('admin.building.edit', [$buildingCode])
                ->with('success', 'Building information has been updated successfully.');

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.building.edit', [$buildingCode])
                ->with('error', 'The requested building could not be found.');

        } catch (\Throwable $e) {

            Log::error('Building update failed.', [
                'building_code' => $buildingCode,
                'request_data'  => $request->except(['_token', '_method']),
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating the building. Please try again later.');
        }
    }
}
