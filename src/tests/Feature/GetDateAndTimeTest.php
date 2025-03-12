<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use App\Models\Status;
use App\Models\User;

class GetDateAndTimeTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * 4 日時取得機能
     *
     * @return void
     */
    public function testDisplayedDateTimeMatchesCurrentDateTime()
    {
        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test1@example.com',
            'password' => 'test_user1',
        ]);

        $response->assertRedirect('/attendance'); // ログイン後のリダイレクト先を確認

        // ログイン状態を確認
        $this->assertAuthenticated();

        // ログインしたユーザーを取得
        $user = auth()->user();

        $now = Carbon::now();
        Carbon::setTestNow($now);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        // 2. 画面に表示されている日時情報を確認する
        $response->assertSee($now->format('Y年n月j日'));
        $response->assertSee($now->isoFormat('ddd'));
        $response->assertSee($now->format('H:i'));

        $response->assertSee($now->format('Y年n月j日') . '(' . $now->isoFormat('ddd') . ')');

        Carbon::setTestNow();
    }
}
