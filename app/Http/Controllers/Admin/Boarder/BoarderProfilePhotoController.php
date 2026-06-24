<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Models\Admin\Boarder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoarderProfilePhotoController extends Controller
{
    public function edit($boarderId){
        $data = Boarder::where('boarder_id', $boarderId)->first();
        return view("admin.boarder.add-boarder.photograph.index",compact("data"));
    }

    public function update(Request $request, string $boarderId)
    {
        $request->validate([
            'boarder_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                //'dimensions:width=300,height=300',
            ],
        ], [
            //'boarder_image.dimensions' => 'Image size must be exactly 300 x 300 pixels.',
        ]);

        try {

            DB::transaction(function () use ($request, $boarderId) {

                $boarder = Boarder::where('boarder_id', $boarderId)
                    ->firstOrFail();

                $file = $request->file('boarder_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($boarder->boarder_image)) {
                    Storage::disk('public')->delete(
                        Boarder::FILE_PATH . '/' . $boarder->boarder_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Boarder::FILE_PATH,
                    $imageName,
                    'public'
                );

                $boarder->update([
                    'boarder_image' => $imageName,
                ]);
            });
            
            return redirect()
            ->route('admin.boarder.profile.photo.edit', $boarderId)
            ->with('success', 'Image updated successfully');

        } catch (ModelNotFoundException $e) {

            return redirect()
            ->route('admin.boarder.profile.photo.edit', $boarderId)
            ->with('error', 'The requested room could not be found.');

        } catch (\Throwable $e) {
            dd($e);
            Log::error('Floor image update failed.', [
                'boarder_id' => $boarderId,
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
