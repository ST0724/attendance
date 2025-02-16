<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\AttendanceRequest;
use App\Models\BreakRequest;
use Illuminate\Support\Facades\DB;

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
        $time = Carbon::now()->format('H:i');

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


    public function attendanceList($year = null, $month = null){
        // 年月が指定されていない場合は現在の年月を使用
        if (!$year || !$month) {
            $now = Carbon::now();
        } else {
            $now = Carbon::createFromDate($year, $month, 1);
        }

        $start_date = $now->copy()->startOfMonth();
        $end_date = $now->copy()->endOfMonth();

        // 前月と翌月の日付を計算
        $prev_month = $now->copy()->subMonth();
        $next_month = $now->copy()->addMonth();

        $records = AttendanceRecord::with('breakRecords')
        ->whereBetween('date', [$start_date, $end_date])
        ->where('user_id', Auth::id())
        ->orderBy('date')
        ->get()
        ->map(function ($record) {
            if ($record->clock_in && $record->clock_out) {
                $clock_in = Carbon::parse($record->clock_in);
                $clock_out = Carbon::parse($record->clock_out);
                
                // 総勤務時間を計算（分単位）
                $total_work_minutes = $clock_out->diffInMinutes($clock_in);
                
                // 休憩時間の合計を計算
                $total_break_minutes = $record->breakRecords->sum(function ($break) {
                    $break_start = Carbon::parse($break->break_start);
                    $break_end = Carbon::parse($break->break_end);
                    return $break_end->diffInMinutes($break_start);
                });
                
                // 実労働時間を計算
                $actual_work_minutes = $total_work_minutes - $total_break_minutes;
                
                // 時間と分に変換
                $hours = floor($actual_work_minutes / 60);
                $minutes = $actual_work_minutes % 60;
                
                $record->total_work_time = sprintf('%d:%02d', $hours, $minutes);
                $record->total_break_time = sprintf('%d:%02d', floor($total_break_minutes / 60), $total_break_minutes % 60);
            } else {
                $record->total_work_time = '';
                $record->total_break_time = '';
            }
            
            return $record;
        });

        return view('attendance_list', compact('records', 'now', 'prev_month', 'next_month'));
    }


    public function attendanceDetail($id){
        $record = AttendanceRecord::with(['breakRecords', 'user', 'attendanceRequests' => function ($query) {
            $query->where('approval', false);
        }])->find($id);

        if ($record) {
            $pending_request = $record->attendanceRequests->first();
            
            if ($pending_request) {
                // approvalがfalseのAttendanceRequestが存在する場合
                $record = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
                    ->where('attendance_record_id', $id)
                    ->where('approval', false)
                    ->first();
            }
        }
        return view('attendance_detail', compact('record'));
    }

    public function attendanceDetailRequest(Request $request, $id){
        $attendance = $request->only(['clock_in', 'clock_out']);
        $attendance['date'] = AttendanceRecord::find($id)->date;
        $attendance['user_id'] = Auth::id();
        $attendance['attendance_record_id'] = $id;
        $attendance['remarks'] = $request->input('remarks');

        $new_request = AttendanceRequest::create($attendance);
        $new_request_id = $new_request->id;

        $breaks = $request->input('breaks');
        foreach ($breaks as $break) {
            $break['user_id'] = Auth::id();
            $break['attendance_request_id'] = $new_request_id;
            $break['break_start'] = Carbon::parse($break['break_start'])->format('H:i');
            $break['break_end'] = Carbon::parse($break['break_end'])->format('H:i');
            BreakRequest::create($break);
        }
        return redirect("/attendance/{$id}");
    }

    public function correctionRequestList(Request $request){
        $tab = $request->tab;

        if ($tab === 'pending') {
            $records = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('user_id', Auth::id())
            ->where('approval', false)
            ->get();
        }else if($tab === 'approved'){
            $records = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('user_id', Auth::id())
            ->where('approval', true)
            ->get();
        }else{
            $records = collect();
        }
        return view('correction_request', compact('records'));
    }
}