<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'attendance_record_id', 'date', 'clock_in', 'clock_out', 'remarks', 'approval'];

    public function user(){
        return $this->belongsTo(User::class);
    }

     public function attendanceRecord(){
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function breakRequests(){
        return $this->hasMany(BreakRequest::class);
    }
}
