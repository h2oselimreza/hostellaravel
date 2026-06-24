<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Boarder\BoarderRequest;
use App\Models\Admin\Boarder;
use App\Models\Admin\SeatAllocation;
use App\Repositories\AdminBoarderRepository;
use App\Repositories\CommonRepository;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BoarderPersonalInfoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create($roomCode, $seatCode)
    {
        return view('admin.boarder.add-boarder.personal.create-edit');
    }

    public function store(
        BoarderRequest $request,
        TokenService $tokenService
    ) {
        $validated = $request->validated();

        if (
            SeatAllocation::where('seat', $validated['seatCode'])->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'seatCode' => 'This seat is already allocated.'
                ]);
        }

        $boarderId = DB::transaction(function () use (
            $validated,
            $tokenService
        ) {

            $boarderId = config('constants.BOARDER_CODE')
                . $tokenService->getTokenByCode(
                    config('constants.BOARDER_CODE')
                );

            $now = Carbon::now();
            $userId = Auth::user()->user_id;

            $isSingle =
                ($validated['marital_status'] ?? null) === 'Single';

            $boarderData = array_merge(
                $validated,
                [
                    'boarder_id' => $boarderId,
                    'anniversary' => $isSingle ? null : ($validated['anniversary'] ?? null),
                    'spouse_name' => $isSingle ? null : ($validated['spouse_name'] ?? null),
                    'spouse_occupation' => $isSingle ? null : ($validated['spouse_occupation'] ?? null),
                    'spouse_office_address' => $isSingle ? null : ($validated['spouse_office_address'] ?? null),
                    'spouse_contact' => $isSingle ? null : ($validated['spouse_contact'] ?? null),
                    'created_by' => $userId,
                    'created_dt_tm' => $now,
                    'updated_by' => $userId,
                    'updated_dt_tm' => $now,
                ]
            );

            unset($boarderData['seatCode']);

            $boarder = Boarder::create($boarderData);

            SeatAllocation::create([
                'seat' => $validated['seatCode'],
                'boarder' => $boarder->boarder_id,
                'allocated_dt_tm' => $now,
                'created_by' => $userId,
                'created_dt_tm' => $now,
                'updated_by' => $userId,
                'updated_dt_tm' => $now,
            ]);

            return $boarderId;
        });

        return redirect()
            ->route(
                'admin.boarder-enrollment.new-boarder.personal.info.edit',
                $boarderId
            )
            ->with(
                'success',
                'Boarder created successfully.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $boarderCode)
    {
        $data = Boarder::where('boarder_id', $boarderCode)->first();
        return view('admin.boarder.add-boarder.personal.create-edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        BoarderRequest $request,
        string $boarderId
    ) {
        $validated = $request->validated();

        $boarder = Boarder::where( 'boarder_id', $boarderId )->firstOrFail();

        $seatExists = SeatAllocation::where(
            'seat',
            $validated['seatCode']
        )->where('boarder', '!=', $boarderId)
        ->exists();

        if ($seatExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'seatCode' => 'This seat is already allocated.'
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $boarder,
        ) {

            $now = now();
            $userId = Auth::user()->user_id;

            $isSingle = ($validated['marital_status'] ?? null) === 'Single';

            $boarderData = array_merge(
                $validated,
                [
                    'anniversary' => $isSingle ? null : ($validated['anniversary'] ?? null),
                    'spouse_name' => $isSingle ? null : ($validated['spouse_name'] ?? null),
                    'spouse_occupation' => $isSingle ? null : ($validated['spouse_occupation'] ?? null),
                    'spouse_office_address' => $isSingle ? null : ($validated['spouse_office_address'] ?? null),
                    'spouse_contact' => $isSingle ? null : ($validated['spouse_contact'] ?? null),
                    'updated_by' => $userId,
                    'updated_dt_tm' => $now,
                ]
            );

            unset($boarderData['seatCode']);
            $boarder->update($boarderData);
        });

        return redirect()
            ->route(
                'admin.boarder-enrollment.new-boarder.personal.info.edit',
                $boarderId
            )
            ->with(
                'success',
                'Boarder updated successfully.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateStatus($id)
    {
        $employee = Boarder::findOrFail($id);
        $employee->is_active = $employee->is_active == 1 ? 0 : 1;
        $employee->save();
        return redirect()->back()->with('success', 'Status Updated Successfully');
    }

        function getCommonTableElement($commonTableElementArr)
    {
        $query = DB::table('common_table');

        if (isset($commonTableElementArr['type'])) {
            $query->where('type', $commonTableElementArr['type']);
        }

        if (isset($commonTableElementArr['depend_on_element'])) {
            $query->where('depend_on_element', $commonTableElementArr['depend_on_element']);
        }

        return $query->orderBy('element_order', 'ASC')
                    ->orderBy('element', 'ASC')
                    ->get();
    }
}
