<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Status;
use App\Models\User;

class BreakButtonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test〇〇()
    {

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
