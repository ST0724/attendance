<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Status;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;

class AttendanceButtonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }


    public function testAttendance()
    {
        // テスト用のユーザーを作成
        User::factory()->create([
            'name'  => 'テスト',
            'email' => 'test@example.com',
            'password' => bcrypt('test_user'),
            'status_id' => 1, // 初期状態は勤務外
        ]);

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'test_user',
        ]);

        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals(1, $user->fresh()->status_id);

        // リダイレクト先のページを取得
        $response = $this->get('/attendance');

        // 「出勤」ボタンの表示確認
        $response->assertSee('出勤', false);
        $response->assertSee('<button class="attendance__button" name="action" value="clock_in">', false);

        // 出勤処理
        $response = $this->post('/attendance', ['action' => 'clock_in']);
        $response->assertRedirect('/attendance');

         // データベースの状態を確認
        $this->assertEquals(2, $user->fresh()->status_id, 'ユーザーのステータスが出勤中に更新されていません');

        // リダイレクト先のページを取得
        $response = $this->get('/attendance');

        // 「出勤中」の表示確認
        $response->assertSee('出勤中', false);
        $response->assertDontSee('勤務外', false);

        // HTML構造の確認
        $response->assertSee('<label class="status__label">出勤中</label>', false);
    }


    protected function tearDown(): void
    {
        $this->artisan('migrate:refresh');
        $this->artisan('cache:clear');
        parent::tearDown();
    }
}
