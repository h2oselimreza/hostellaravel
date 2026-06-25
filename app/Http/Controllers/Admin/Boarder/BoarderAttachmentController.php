<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Models\Admin\Boarder;
use App\Models\Admin\BoarderFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoarderAttachmentController extends Controller
{
    public function edit($boarderId){
        $files = BoarderFile::where(
            ['boarder' => $boarderId,
            'file_type' => 'attachment',
            'is_active' => 1
            ])
        ->orderBy('created_dt_tm', 'desc')->get();

        $data = Boarder::where('boarder_id', $boarderId)->first();
        return view('admin.boarder.add-boarder.attachment.index', compact('data','files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'boarderId' => [
                'required',
                'exists:boarder,boarder_id',
            ],
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB
            ],
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

                $boarder = Boarder::where(
                    'boarder_id',
                    $validated['boarderId']
                )->first();

                $file = $request->file('file');

                $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                // Upload file
                $file->storeAs(
                    BoarderFile::FILE_PATH,
                    $fileName,
                    'public'
                );
                
                $insertArr = [
                    'boarder'      => $boarder->boarder_id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_type'     => config('constants.ATTACHMENT_FILE', 'attachment'),
                ];

                BoarderFile::create($insertArr);
            });

            return response()->json([
                'success' => true,
            ]);

        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('admin.boarder-enrollment.boarder')
                ->with(
                    'error',
                    'The requested boarder could not be found.'
                );

        } catch (\Throwable $e) {

            Log::error('Boarder attachment upload failed.', [
                'boarder_id' => $validated['boarderId'] ?? null,
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

                $file = BoarderFile::where([
                    'id'        => $id,
                    'file_type' => 'attachment',
                ])->firstOrFail();

                // Delete physical file
                Storage::disk('public')->delete(
                    BoarderFile::FILE_PATH . '/' . $file->file_name
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

            Log::error('Boarder attachment deletion failed.', [
                'id' => $id,
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
