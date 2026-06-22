<?php

namespace App\Http\Controllers\Admin\Seat;

use App\Http\Controllers\Controller;
use App\Models\Admin\Seat;
use App\Models\Admin\SeatFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeatAdditionalImageController extends Controller
{
    public function edit($seatCode){
        $files = SeatFile::where(
            ['seat' => $seatCode,
            'file_type'=>'attachment',
            'is_active'=>1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Seat::where('seat_code', $seatCode)->first();
        return view('admin.seat.additional-image.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seatCode' => [
                'required',
                'exists:hst_seat,seat_code',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $seat = Seat::where(
                    'seat_code',
                    $validated['seatCode']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    SeatFile::IMAGE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'seat'      => $seat->seat_code,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                SeatFile::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.seat.index')
                ->with(
                    'error',
                    'The requested seat could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Seat attachment upload failed.', [
                'seat_code' => $validated['seatCode'] ?? null,
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

                $file = SeatFile::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    SeatFile::IMAGE_PATH . '/' . $file->file_name
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

            Log::error('Seat attachment deletion failed.', [
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
