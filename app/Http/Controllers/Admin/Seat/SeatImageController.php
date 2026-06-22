<?php

namespace App\Http\Controllers\Admin\Seat;

use App\Http\Controllers\Controller;
use App\Models\Admin\Seat;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeatImageController extends Controller
{
    public function edit($seatCode){
        $data = Seat::where('seat_code', $seatCode)->first();
        return view('admin.seat.profile-picture.index',compact('data'));
    }

    public function update(Request $request, string $seatCode)
    {
        $request->validate([
            'seat_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                //'dimensions:width=300,height=300',
            ],
        ], ['seat_image.dimensions' => 'Image size must be exactly 300 x 300 pixels.',]
        );

        try {

            DB::transaction(function () use ($request, $seatCode) {

                $seat = Seat::where('seat_code', $seatCode)
                    ->firstOrFail();

                $file = $request->file('seat_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($seat->seat_image)) {
                    Storage::disk('public')->delete(
                        Seat::IMAGE_PATH . '/' . $seat->seat_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Seat::IMAGE_PATH,
                    $imageName,
                    'public'
                );

                $seat->update([
                    'seat_image' => $imageName,
                ]);
            });

            return redirect()
                ->route('admin.seat.image.edit', $seatCode)
                ->with(
                    'success',
                    'Seat image has been updated successfully.'
                );

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.seat.index')
                ->with('error', 'The requested seat could not be found.');

        } catch (\Throwable $e) {
            dd($e);
            Log::error('Seat image update failed.', [
                'seat_code' => $seatCode,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'An unexpected error occurred while updating the seat image. Please try again later.'
                );
        }
    }
}
