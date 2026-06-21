<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Admin\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RoomImageController extends Controller
{
    public function edit($roomCode){
        $data = Room::where('room_code', $roomCode)->first();
        return view('admin.room.profile-picture.index',compact('data'));
    }

    public function update(Request $request, string $roomCode)
    {
        $request->validate([
            'room_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                'dimensions:width=300,height=300',
            ],
        ], [
            'room_image.dimensions' => 'Image size must be exactly 300 x 300 pixels.',
        ]);

        try {

            DB::transaction(function () use ($request, $roomCode) {

                $room = Room::where('room_code', $roomCode)
                    ->firstOrFail();

                $file = $request->file('room_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($room->room_image)) {
                    Storage::disk('public')->delete(
                        Room::IMAGE_PATH . '/' . $room->room_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Room::IMAGE_PATH,
                    $imageName,
                    'public'
                );

                $room->update([
                    'room_image' => $imageName,
                ]);
            });

            return redirect()
                ->route('admin.room.profile-picture.edit', $roomCode)
                ->with(
                    'success',
                    'Room image has been updated successfully.'
                );

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.room.index')
                ->with('error', 'The requested room could not be found.');

        } catch (\Throwable $e) {

            Log::error('Floor image update failed.', [
                'building_code' => $roomCode,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'An unexpected error occurred while updating the room image. Please try again later.'
                );
        }
    }
}
