<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $userRow = Timelog::find(auth()->user()->id);
        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;
        return view('dashboard', ['user' => auth()->user(), 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus]);
    }

    public function startWorking(Request $request)
    {
        $timestamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->StartBreak = null;
        $userRow->EndBreak = null;
        $userRow->StopWork = null;
        $userRow->StartWork = $timestamp;
        $userRow->ShiftStatus = true;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function break()
    {
        $timeStamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = true;
        $userRow->StartBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function stopBreak()
    {
        $timeStamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = false;

        $userRow->EndBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $timeStamp = now();
        $breakStatus = true;
        $userRow = Timelog::find(auth()->user()->id);
        if ($userRow->BreakStatus == true) {
            $breakStatus = true;
            $userRow->BreakStatus = false;
        }
        $userRow->ShiftStatus = false;
        $userRow->StopWork = $timeStamp;
        $userRow->save();

        $this->makeTimeSheet($userRow, $breakStatus, $timeStamp);

        return redirect('/dashboard');
    }

    public function makeTimeSheet($userRow, $breakStatus, $timeStamp)
    {
        $newTimeSheet = new Timesheet;
        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;
        if ($breakStatus == true) {
            $newTimeSheet->BreakStart = $userRow->StartBreak;
            $newTimeSheet->BreakStop = $userRow->EndBreak;
            $newTimeSheet->BreakHours = $this->calculateBreakHours($newTimeSheet, $userRow);
        }
        $newTimeSheet->RegularHours = $this->calculateRegularHours($userRow);
        // $newTimeSheet->OverTime = $this->calculateOverTime($newTimeSheet);
        $newTimeSheet->Month = $timeStamp;
        $newTimeSheet->save();
    }

    public function calculateBreakHours($userRow)
    {
        $start = $userRow->StartBreak;
        $end = $userRow->EndBreak;
        $startParse = Carbon::parse($start);
        $endParse = Carbon::parse($end);

        $diffInMin = $endParse->diffInMinutes($startParse);
        $decimalTime = round($diffInMin / 60, 2);
        return $decimalTime;
    }
    public function calculateRegularHours($userRow)
    {
        $start = $userRow->StartWork;
        $end = $userRow->StopWork;
        $startParse = Carbon::parse($start);
        $endParse = Carbon::parse($end);
        $diffInMin = $endParse->diffInMinutes($startParse);
        $decimalTime = round($diffInMin / 60, 2);
        return $decimalTime;
    }
    // public function calculateOverTime($newTimeSheet)
    // {

    // }
}
