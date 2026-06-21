<?php

namespace App\Http\Controllers\Admin\Floor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Floor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FloorImageController extends Controller
{
    public function edit($floorCode){
        $data = Floor::where('floor_code', $floorCode)->first();
        return view('admin.floor.profile-picture.index',compact('data'));
    }

    public function update(Request $request, string $floorCode)
    {
        $validated = $request->validate([
            'floor_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $floorCode) {

                $floor = Floor::where('floor_code', $floorCode)
                    ->firstOrFail();

                $file = $request->file('floor_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($floor->floor_image)) {
                    Storage::disk('public')->delete(
                        Floor::IMAGE_PATH . '/' . $floor->floor_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Floor::IMAGE_PATH,
                    $imageName,
                    'public'
                );

                $floor->update([
                    'floor_image' => $imageName,
                ]);
            });

            return redirect()
                ->route('admin.floor.profile-picture.edit', $floorCode)
                ->with(
                    'success',
                    'Floor image has been updated successfully.'
                );

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.floor.index')
                ->with('error', 'The requested floor could not be found.');

        } catch (\Throwable $e) {

            Log::error('Floor image update failed.', [
                'building_code' => $floorCode,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'An unexpected error occurred while updating the floor image. Please try again later.'
                );
        }
    }
}
