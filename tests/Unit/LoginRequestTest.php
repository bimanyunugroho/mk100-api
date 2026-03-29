<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\Auth\LoginFormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new LoginFormRequest())->rules());
    }

    public function test_email_wajib_diisi(): void
    {
        $v = $this->validate(['email' => '', 'password' => 'password123']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('email', $v->errors()->toArray());
    }

    public function test_email_harus_format_valid(): void
    {
        $v = $this->validate(['email' => 'bukan-email', 'password' => 'password123']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('email', $v->errors()->toArray());
    }

    public function test_password_wajib_diisi(): void
    {
        $v = $this->validate(['email' => 'test@gmail.com', 'password' => '']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('password', $v->errors()->toArray());
    }

    public function test_token_name_nullable(): void
    {
        $v = $this->validate([
            'email'    => 'test@gmail.com',
            'password' => 'password123',
        ]);
        $this->assertFalse($v->fails());
    }

    public function test_token_name_maksimal_100_karakter(): void
    {
        $v = $this->validate([
            'email'      => 'test@gmail.com',
            'password'   => 'password123',
            'token_name' => str_repeat('a', 101),
        ]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('token_name', $v->errors()->toArray());
    }

    public function test_payload_valid_lolos_validasi(): void
    {
        $v = $this->validate([
            'email'    => 'test@gmail.com',
            'password' => 'password123',
        ]);
        $this->assertFalse($v->fails());
    }
}
