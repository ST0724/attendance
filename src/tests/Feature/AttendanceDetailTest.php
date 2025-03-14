<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use Carbon\Carbon;
use App\Models\Status;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test1@example.com',
            'password' => 'test_user1',
        ]);
        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();
    }

    /**
     * 10 勤怠詳細情報取得機能（一般ユーザー）
     *
     * @return void
     */
    public function testAttendanceDetailView()
    {
        $user = auth()->user();

        // 特定の時刻を指定して勤怠データ作成
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-01-01',
            'clock_in' => '09:00:00',
            'clock_out' => '17:00:00'
        ]);

        //上記に対応する休憩データも作成
        $break = BreakRecord::factory()->create([
            'user_id' => $user->id,
            'attendance_record_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:30:00'
        ]);
    
        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('attendance_records', [
            'id' => $attendance->id,
            'user_id' => $user->id,
            'date' => '2024-01-01',
            'clock_in' => '09:00:00',
            'clock_out' => '17:00:00'
        ]);

        $this->assertDatabaseHas('break_records', [
            'id' => $break->id,
            'user_id' => $user->id,
            'attendance_record_id' => $break->id,
            'break_start' => '12:00:00',
            'break_end' => '13:30:00'
        ]);

        $id = $attendance->id;
        $response = $this->get("/attendance/{$id}");
        $response->assertStatus(200);

        //名前の確認
        $response->assertSee($user->name);

        //日付の確認
        $fomat_year = Carbon::parse($attendance->date)->format('Y年');
        $fomat_date = Carbon::parse($attendance->date)->format('n月j日');
        $response->assertSee($fomat_year);
        $response->assertSee($fomat_date);

        //出勤時間・退勤時間の確認
        $format_clock_in = Carbon::parse($attendance->clock_in)->format('H:i');
        $format_clock_out =Carbon::parse($attendance->clock_out)->format('H:i');
        $response->assertSee($format_clock_in);
        $response->assertSee($format_clock_out);

        //休憩の確認
        $format_break_start = Carbon::parse($break->break_start)->format('H:i');
        $format_break_end =Carbon::parse($break->break_end)->format('H:i');
        $response->assertSee($format_break_start);
        $response->assertSee($format_break_end);
    }

    protected function tearDown(): void
    {
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->artisan('migrate:refresh');
        $this->artisan('cache:clear');
        parent::tearDown();
    }
}
