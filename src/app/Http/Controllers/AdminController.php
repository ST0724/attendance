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
            return $this->getTotalWorkTime($record);
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
        $user = User::where('id', $id)->first();

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

        return view('admin.admin_attendance_staff', compact('user','records', 'now', 'prev_month', 'next_month'));
    }


    public function adminCorrectionRequestList(Request $request){
        $tab = $request->tab;

        if ($tab === 'pending') {
            $records = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('approval', false)
            ->get();
        }else if($tab === 'approved'){
            $records = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('approval', true)
            ->get();
        }else{
            $records = collect();
        }
        return view('correction_request', compact('records'));
    }


    public function adminApproval($request_id){
        $record = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('id', $request_id)
            ->first();

        return view('admin.approval', compact('record'));
    }

    public function adminApprovalUpdate(Request $request, $request_id){
        $attendance_request = AttendanceRequest::with(['breakRequests', 'user', 'attendanceRecord'])
            ->where('id', $request_id)
            ->first();

        // $attendnce_record = AttendanceRecord::with('user', 'breakRecords')
        //     ->where('id', $attendance_request['attendance_record_id'])
        //     ->first();
        
        AttendanceRecord::find($attendance_request['attendance_record_id'])->update([
            'clock_in' => $attendance_request->clock_in,
            'clock_out' => $attendance_request->clock_out,
        ]);

        $break_requests = $attendance_request->breakRequests;
        foreach ($break_requests as $break_request) {
            BreakRecord::find($break_request['break_record_id'])->update([
                'break_start' => $break_request->break_start,
                'break_end' => $break_request->break_end,
            ]);
        }
            
        $record['approval'] = 1;
        $record['approval'] = (bool)$record['approval'];

        AttendanceRequest::find($request_id)->update(['approval' => $record['approval']]);

        return redirect("/admin/stamp_correction_request/approve/{$request_id}");
    }
}