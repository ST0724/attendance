<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'attendance_request_id', 'break_record_id', 'break_start', 'break_end'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function breakRecord(){
        return $this->belongsTo(BreakRecord::class);
    }

    public function attendanceRequest(){
        return $this->belongsTo(AttendanceRequest::class);
    }
}
