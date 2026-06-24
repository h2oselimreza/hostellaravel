<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\BoarderRepository;
use Illuminate\Http\Request;

class NewBoarderController extends Controller
{
    public function index(BoarderRepository $boarderRepository)
    {

        $data = $boarderRepository->getRooms(
            roomCode: null,
            roomCodeArr: [],
            flag: 1
        );
        return view('admin.boarder.room-list.index', compact('data'));
    }

    public function seatList($roomCode, BoarderRepository $boarderRepository){

        $seats = $boarderRepository->getRoomSeats($roomCode);
        //dd($seats);
        return view('admin.boarder.seat-list.index', compact('roomCode','seats'));
    }
}
