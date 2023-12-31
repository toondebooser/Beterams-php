<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;

class MyWorkersController extends Controller
{
    public function fetchWorkers()
    {
        $workers = User::with('timelogs')->get();
        $setForTimesheet = true;
        return view('my-workers', ['workers' => $workers, 'setForTimesheet' => $setForTimesheet]);
    }
}
