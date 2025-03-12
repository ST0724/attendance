<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * 9 勤怠一覧情報取得機能（一般ユーザー）
     *
     * @return void
     */
    public function testAttendanceListFetch()
    {
        // ログイン処理
        $response = $this->post('/login', [
            'email' => 'test1@example.com',
            'password' => 'test_user1',
        ]);
        $response->assertRedirect('/attendance'); 
        $this->assertAuthenticated();


        //勤怠一覧を開く
        $response = $this->get('http://localhost/attendance/list');
        $response->assertStatus(200);
        
        //勤怠情報の取得・確認
    }

    protected function tearDown(): void
    {
        $this->artisan('migrate:refresh');
        $this->artisan('cache:clear');
        parent::tearDown();
    }
}
