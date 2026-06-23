<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Admin\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VendorImageController extends Controller
{
    public function edit($vendorCode){
        $data = Vendor::where('vendor_code', $vendorCode)->first();
        return view('admin.master-data.vendor.image.index',compact('data'));
    }

    public function update(Request $request, string $vendorCode)
    {
        $request->validate([
            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                //'dimensions:width=300,height=300',
            ],
        ], [
            //'profile_image.dimensions' => 'Image size must be exactly 300 x 300 pixels.',
        ]);

        try {

            DB::transaction(function () use ($request, $vendorCode) {

                $vendor = Vendor::where('vendor_code', $vendorCode)
                    ->firstOrFail();

                $file = $request->file('profile_image');

                $imageName = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                // Delete old image
                if (!empty($vendor->profile_image)) {
                    Storage::disk('public')->delete(
                        Vendor::FILE_PATH . '/' . $vendor->profile_image
                    );
                }

                // Upload new image
                $file->storeAs(
                    Vendor::FILE_PATH,
                    $imageName,
                    'public'
                );

                $vendor->update([
                    'profile_image' => $imageName,
                ]);
            });

            return redirect()
                ->route('admin.vendor.image.edit', $vendorCode)
                ->with(
                    'success',
                    'Vendor image has been updated successfully.'
                );

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.vendor.index')
                ->with('error', 'The requested vendor could not be found.');

        } catch (\Throwable $e) {

            Log::error('Venor image update failed.', [
                'vendor_code' => $vendorCode,
                'error'         => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'An unexpected error occurred while updating the vendor image. Please try again later.'
                );
        }
    }
}
