<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Admin\Room;
use App\Models\Admin\RoomFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomAdditionalImageController extends Controller
{
    public function edit($roomCode){
        $files = RoomFile::where(
            ['room' => $roomCode,
            'file_type'=>'attachment',
            'is_active'=>1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Room::where('room_code', $roomCode)->first();
        return view('admin.room.additional-image.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roomCode' => [
                'required',
                'exists:hst_room,room_code',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $building = Room::where(
                    'room_code',
                    $validated['roomCode']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    RoomFile::IMAGE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'room'      => $building->room_code,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                RoomFile::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.room.index')
                ->with(
                    'error',
                    'The requested room could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Room attachment upload failed.', [
                'room_code' => $validated['roomCode'] ?? null,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to upload the attachment at this time. Please try again later.'
                );
        }
    }

    public function destroy(int $id)
    {
        try {

            DB::transaction(function () use ($id) {

                $file = RoomFile::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    RoomFile::IMAGE_PATH . '/' . $file->file_name
                );

                // Delete database record
                $file->delete();
            });

            return back()->with(
                'success',
                'Attachment has been deleted successfully.'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return back()->with(
                'error',
                'The requested attachment could not be found.'
            );

        } catch (\Throwable $e) {

            Log::error('Workshop attachment deletion failed.', [
                'attachment_id' => $id,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()->with(
                'error',
                'Unable to delete the attachment at this time. Please try again later.'
            );
        }
    }
}
