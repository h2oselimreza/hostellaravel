<?php

namespace App\Http\Controllers\Admin\Building;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BuildingProfilePictureController extends Controller
{
    public function edit($buildingCode){
        $data = Building::where('building_code', $buildingCode)->first();
        return view('admin.building.profile-picture.index',compact('data'));
    }

    public function update(Request $request, string $buildingCode)
    {
        $validated = $request->validate([
            'building_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $buildingCode) {

                $building = Building::where('building_code', $buildingCode)
                    ->firstOrFail();

                $file = $request->file('building_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($building->building_image)) {
                    Storage::disk('public')->delete(
                        Building::IMAGE_PATH . '/' . $building->building_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Building::IMAGE_PATH,
                    $imageName,
                    'public'
                );

                $building->update([
                    'building_image' => $imageName,
                ]);
            });

            return redirect()
                ->route('admin.building.profile-picture.edit', $buildingCode)
                ->with(
                    'success',
                    'Building image has been updated successfully.'
                );

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.building.index')
                ->with('error', 'The requested building could not be found.');

        } catch (\Throwable $e) {

            Log::error('Building image update failed.', [
                'building_code' => $buildingCode,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'An unexpected error occurred while updating the building image. Please try again later.'
                );
        }
    }
}
