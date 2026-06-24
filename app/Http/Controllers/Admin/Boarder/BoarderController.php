<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Repositories\BoarderRepository;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;

class BoarderController extends Controller
{

    public function index( BoarderRepository $boarderRepository )
    {
    
        $boarders = $boarderRepository->getBoarders([
            'isActive' => null,
        ]);

        $seats = $boarderRepository->getVacantSeats();
        
        return view('admin.boarder.index', compact('boarders','seats'));
    }
}
