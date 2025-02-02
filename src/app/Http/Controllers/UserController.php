<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;

class UserController extends Controller
{
    public function attendance(){
        $status = Auth::user()->status->name;
        $now = Carbon::now();
        return view('attendance', compact('now', 'status'));
    }


    public function attendanceStore(Request $request){
        $now = Carbon::now();
        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->toTimeString();

        //dd($request);

        switch ($request->input('action')) {
            case 'clock_in':
                $record['user_id'] = Auth::id();
                $record['date'] = $date;
                $record['clock_in'] = $time;
                AttendanceRecord::create($record);
                Auth::user()->update(['status_id' => '2']);
                break;

            case 'clock_out':
                AttendanceRecord::whereDate('date', $date)
                ->where('user_id', Auth::id())->first()->update(['clock_out' => $time]);
                Auth::user()->update(['status_id' => '4']);
                break;

            case 'break_start':
                $record['user_id'] = Auth::id();
                $record['attendance_record_id'] = AttendanceRecord::whereDate('date', $date)
                ->where('user_id', Auth::id())->value('id');
                $record['break_start'] = $time;
                BreakRecord::create($record);
                Auth::user()->update(['status_id' => '3']);
                break;

            case 'break_end':
                $breakRecord = BreakRecord::whereHas('attendanceRecord', function($query) use ($date) {
                    $query->whereDate('date', $date);
                })
                ->where('user_id', Auth::id())->whereNull('break_end')->first()->update(['break_end' => $time]);
                Auth::user()->update(['status_id' => '2']);
                break;
        }

        return redirect('/attendance');
        
    }
}
