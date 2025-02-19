<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Traits\CommonTrait;

class AdminController extends Controller
{
    use CommonTrait;

    public function showAdminLogin(){
        return view('auth.admin_login');
    }

    public function adminLogin(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin/attendance/list');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function adminAttendanceList($year = null, $month = null, $day =null){
        // 日付が指定されていない場合は現在の日付を使用
        if (!$year || !$month || !$day) {
            $now = Carbon::now();
        } else {
            $now = Carbon::createFromDate($year, $month, $day);
        }

        $prev_day = $now->copy()->subDay();
        $next_day = $now->copy()->addDay();

        $records = AttendanceRecord::with('breakRecords','user')
        ->whereDate('date', $now->toDateString())
        ->orderBy('clock_in')
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

        return view('admin.admin_attendance_list', compact('records', 'now', 'prev_day', 'next_day'));
    }


    public function adminAttendanceDetail($id){
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


    public function adminStaffList(){
        $users = User::all();
        return view('admin.admin_staff', compact('users'));
    }

    public function adminAttendanceStaff($id, $year = null, $month = null){
        $user_name = User::where('id', $id)->first()->name;

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

        $records = AttendanceRecord::with('user', 'breakRecords')
        ->whereBetween('date', [$start_date, $end_date])
        ->where('user_id', $id)
        ->orderBy('date')
        ->get()
        ->map(function ($record) {
            return $this->getTotalWorkTime($record);
        });

        return view('admin.admin_attendance_staff', compact('user_name','records', 'now', 'prev_month', 'next_month'));
    }
}
