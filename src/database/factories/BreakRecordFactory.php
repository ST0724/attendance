<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BreakRecord;
use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class BreakRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $record = AttendanceRecord::inRandomOrder()->first();
        $clock_in = Carbon::parse($record->date . ' ' . $record->clock_in);
        $clock_out = Carbon::parse($record->date . ' ' . $record->clock_out);

        $break_start = $clock_in->copy()->addMinutes(rand(30, $clock_out->diffInMinutes($clock_in) - 60));

        $max_break_duration = $clock_out->diffInMinutes($break_start);
        $break_duration = rand(15, min(60, $max_break_duration));
        $break_end = $break_start->copy()->addMinutes($break_duration);

        return [
        'attendance_record_id' => $record->id,
        'user_id' => $record->user_id,
        'break_start' => $break_start->format('H:i'),
        'break_end' => $break_end->format('H:i'),
        ];
    }
}
