<?php

namespace App\Traits;

use Carbon\Carbon;

trait CommonTrait
{
    public function getTotalWorkTime($record)
    {
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
        }
}