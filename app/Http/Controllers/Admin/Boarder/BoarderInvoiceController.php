<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Models\Admin\Boarder;
use App\Repositories\BoarderRepository;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;

class BoarderInvoiceController extends Controller
{
    // public function edit($boarderId){
    //     $data = Boarder::where('boarder_id', $boarderId)->first();
    //     return view("admin.boarder.add-boarder.invoice.index",compact("data"));
    // }

    public function edit(
        $boarderId,
        CommonRepository $commonRepository, 
        BoarderRepository $boarderRepository)
    {
        $data['disableFlag'] = '';

        $data['data'] = Boarder::where('boarder_id', $boarderId)->first();

        $boarderDetails = $boarderRepository->getBoarderPersonalInfo($boarderId, null, 1);

        if (!$boarderId || !$boarderDetails) {
            return redirect()->route('admin.boarder-enrollment.create');
        }

        $data['itemHeads'] = $commonRepository->getItemHead(1);

        $data['templateDetails'] = $boarderRepository->getInvoiceTemplate($boarderId);

        $data['boarderId'] = $boarderId;

        $data['itemHeadInfo'] = $boarderRepository->getAdmissionHead(config('constants.ADMISSION_FEE_HEAD_CODE'));

        $data['admissionFeeInfo'] = $boarderRepository->getAdmissionFee($boarderId);

        $data['boarderDetails'] = $boarderDetails;

        if (
            !empty($data['admissionFeeInfo']) &&
            isset($data['admissionFeeInfo'][0]) &&
            $data['admissionFeeInfo']->is_paid != 0
        ) {
            $data['disableFlag'] = 'disabled';
        }

        return view(
            'admin.boarder.add-boarder.invoice.index',
            $data
        );
    }
}
