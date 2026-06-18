<?php

namespace App\Http\Controllers\Admin\Building;

use App\Http\Controllers\Controller;
use App\Models\Admin\Building;
use App\Models\Admin\BuildingFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BuildingAdditionalImagesController extends Controller
{
    public function edit($buildingCode){
        $files = BuildingFile::where(
            ['building' => $buildingCode,
            'file_type'=>'attachment',
            'is_active'=>1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Building::where('building_code', $buildingCode)->first();
        return view('admin.building.additional-image.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buildingCode' => [
                'required',
                'exists:hst_building,building_code',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $building = Building::where(
                    'building_code',
                    $validated['buildingCode']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    BuildingFile::IMAGE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'building'      => $building->building_code,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                BuildingFile::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.building.index')
                ->with(
                    'error',
                    'The requested building could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Building attachment upload failed.', [
                'building_code' => $validated['buildingCode'] ?? null,
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

                $file = BuildingFile::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    BuildingFile::IMAGE_PATH . '/' . $file->file_name
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
