<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'attendance_record_id', 'break_start', 'break_end'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attendanceRecord(){
        return $this->belongsTo(AttendanceRecord::class);
    }
}
