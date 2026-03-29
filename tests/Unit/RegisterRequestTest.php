<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\Auth\RegisterFormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new RegisterFormRequest())->rules());
    }

    private function validEmployerPayload(array $override = []): array
    {
        return array_merge([
            'name'                  => 'TESTING SI EMPLOYER',
            'email'                 => 'employersaja@gmail.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'employer',
            'company_name'          => 'PT. Testing',
        ], $override);
    }

    private function validFreelancerPayload(array $override = []): array
    {
        return array_merge([
            'name'                  => 'TESTING SI FREELANCER',
            'email'                 => 'freelancer@gmail.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'freelancer',
        ], $override);
    }

    public function test_name_wajib_diisi(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['name' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('name', $v->errors()->toArray());
    }

    public function test_name_maksimal_100_karakter(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['name' => str_repeat('a', 101)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('name', $v->errors()->toArray());
    }

    public function test_email_wajib_diisi(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['email' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('email', $v->errors()->toArray());
    }

    public function test_email_harus_format_valid(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['email' => 'bukan-email']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('email', $v->errors()->toArray());
    }

    public function test_email_harus_unik(): void
    {
        $this->createFreelancer(['email' => 'sudah@gmail.com']);

        $v = $this->validate($this->validFreelancerPayload(['email' => 'sudah@gmail.com']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('email', $v->errors()->toArray());
    }

    public function test_password_wajib_diisi(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['password' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('password', $v->errors()->toArray());
    }

    public function test_password_minimal_8_karakter(): void
    {
        $v = $this->validate($this->validFreelancerPayload([
            'password'              => '1234567',
            'password_confirmation' => '1234567',
        ]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('password', $v->errors()->toArray());
    }

    public function test_password_harus_confirmed(): void
    {
        $v = $this->validate($this->validFreelancerPayload([
            'password'              => 'password123',
            'password_confirmation' => 'berbeda123',
        ]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('password', $v->errors()->toArray());
    }

    public function test_role_wajib_diisi(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['role' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('role', $v->errors()->toArray());
    }

    public function test_role_harus_employer_atau_freelancer(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['role' => 'admin']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('role', $v->errors()->toArray());
    }

    public function test_role_employer_valid(): void
    {
        $v = $this->validate($this->validEmployerPayload());
        $this->assertFalse($v->fails());
    }

    public function test_role_freelancer_valid(): void
    {
        $v = $this->validate($this->validFreelancerPayload());
        $this->assertFalse($v->fails());
    }

    public function test_company_name_nullable(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['company_name' => null]));
        $this->assertFalse($v->fails());
    }

    public function test_company_name_maksimal_100_karakter(): void
    {
        $v = $this->validate($this->validEmployerPayload(['company_name' => str_repeat('a', 101)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('company_name', $v->errors()->toArray());
    }

    public function test_phone_nullable(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['phone' => null]));
        $this->assertFalse($v->fails());
    }

    public function test_phone_maksimal_13_karakter(): void
    {
        $v = $this->validate($this->validFreelancerPayload(['phone' => str_repeat('0', 14)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('phone', $v->errors()->toArray());
    }

    public function test_payload_employer_lengkap_valid(): void
    {
        $v = $this->validate($this->validEmployerPayload(['phone' => '08123456789']));
        $this->assertFalse($v->fails());
    }

    public function test_payload_freelancer_minimal_valid(): void
    {
        $v = $this->validate($this->validFreelancerPayload());
        $this->assertFalse($v->fails());
    }
}
