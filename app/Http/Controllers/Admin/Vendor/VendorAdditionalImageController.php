<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Vendor;
use App\Models\Admin\VendorFiles;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorAdditionalImageController extends Controller
{
    public function edit($vendorCode){
        $files = VendorFiles::where(
            ['vendor' => $vendorCode,
            'file_type'=>'attachment',
            'is_active'=>1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Vendor::where('vendor_code', $vendorCode)->first();
        return view('admin.master-data.vendor.additional-image.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendorCode' => [
                'required',
                'exists:vendor,vendor_code',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $vendor = Vendor::where(
                    'vendor_code',
                    $validated['vendorCode']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    VendorFiles::FILE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'vendor'      => $vendor->vendor_code,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                VendorFiles::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.vendor.index')
                ->with(
                    'error',
                    'The requested vendor could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Vendor attachment upload failed.', [
                'vendor_code' => $validated['vendorCode'] ?? null,
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

                $file = VendorFiles::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    VendorFiles::FILE_PATH . '/' . $file->file_name
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

            Log::error('Vendor attachment deletion failed.', [
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
