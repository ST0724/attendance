<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Status;
use App\Models\User;

class CheckStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ステータス確認機能 勤務外
    public function testStatusOff()
    {
        // テスト用のユーザーを作成
        User::factory()->create([
            'name'  => '勤務外の人',
            'email' => 'test_status1@example.com',
            'password' => bcrypt('test_status_user1'),
            'status_id' => 1, // 初期状態は勤務外
        ]);

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test_status1@example.com',
            'password' => 'test_status_user1',
        ]);

        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals(1, $user->fresh()->status_id);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('勤務外');

        // ログアウト処理
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }


    // ステータス確認機能 出勤中
    public function testStatusWork()
    {
        // テスト用のユーザーを作成
        User::factory()->create([
            'name'  => '出勤中の人',
            'email' => 'test_status2@example.com',
            'password' => bcrypt('test_status_user2'),
            'status_id' => 2, // 初期状態は出勤中
        ]);

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test_status2@example.com',
            'password' => 'test_status_user2',
        ]);

        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals(2, $user->fresh()->status_id);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('出勤中');

        // ログアウト処理
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }


    // ステータス確認機能 休憩中
    public function testStatusBreak()
    {
        // テスト用のユーザーを作成
        User::factory()->create([
            'name'  => '休憩中の人',
            'email' => 'test_status3@example.com',
            'password' => bcrypt('test_status_user3'),
            'status_id' => 3, // 初期状態は休憩中
        ]);

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test_status3@example.com',
            'password' => 'test_status_user3',
        ]);

        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals(3, $user->fresh()->status_id);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('休憩中');

        // ログアウト処理
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }


    // ステータス確認機能 退勤済
    public function testStatusFinish()
    {
        // テスト用のユーザーを作成
        User::factory()->create([
            'name'  => '退勤済の人',
            'email' => 'test_status4@example.com',
            'password' => bcrypt('test_status_user4'),
            'status_id' => 4, // 初期状態は退勤済
        ]);

        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test_status4@example.com',
            'password' => 'test_status_user4',
        ]);

        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals(4, $user->fresh()->status_id);

        // 勤怠打刻画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('退勤済');

        // ログアウト処理
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }


    protected function tearDown(): void
    {
        // データベースをリフレッシュ
        $this->artisan('migrate:refresh');

        // キャッシュをクリア
        $this->artisan('cache:clear');

        // 親クラスのtearDownを呼び出す
        parent::tearDown();
    }
}
