<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $date = $this->getUniqueDate($user->id);

        $clock_in = Carbon::parse($date)->setTime(rand(7, 9), rand(0, 59), 0);

        return [
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $clock_in->format('H:i'),
            'clock_out' => function () use ($clock_in) {
                return $clock_in->copy()->addHours(rand(6, 10))->format('H:i');
            },
        ];
    }

    private function getUniqueDate($user_id)
    {
        do {
            $date = Carbon::now()->subDays(rand(0, 60))->format('Y-m-d');
            $exists = AttendanceRecord::where('user_id', $user_id)
                                ->where('date', $date)
                                ->exists();
        } while ($exists);

        return $date;
    }
}
