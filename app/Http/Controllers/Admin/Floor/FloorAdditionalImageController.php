<?php

namespace App\Http\Controllers\Admin\Floor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Floor;
use App\Models\Admin\FloorFiles;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FloorAdditionalImageController extends Controller
{
    public function edit($floorCode){
        $files = FloorFiles::where(
            ['floor' => $floorCode,
            'file_type'=>'attachment',
            'is_active'=>1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Floor::where('floor_code', $floorCode)->first();
        return view('admin.floor.additional-image.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'floorCode' => [
                'required',
                'exists:hst_floor,floor_code',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $building = Floor::where(
                    'floor_code',
                    $validated['floorCode']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    FloorFiles::IMAGE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'floor'      => $building->floor_code,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                FloorFiles::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.floor.index')
                ->with(
                    'error',
                    'The requested floor could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Floor attachment upload failed.', [
                'floor_code' => $validated['floorCode'] ?? null,
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

                $file = FloorFiles::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    FloorFiles::IMAGE_PATH . '/' . $file->file_name
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
