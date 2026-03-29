<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\Job\StoreJobRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreJobRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new StoreJobRequest())->rules());
    }

    private function validPayload(array $override = []): array
    {
        return array_merge([
            'title'       => 'Backend Developer Laravel',
            'description' => 'Deskripsi pekerjaan lengkap.',
            'type'        => 'freelancer',
            'status'      => 'draft',
        ], $override);
    }

    public function test_title_wajib_diisi(): void
    {
        $v = $this->validate($this->validPayload(['title' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('title', $v->errors()->toArray());
    }

    public function test_title_maksimal_100_karakter(): void
    {
        $v = $this->validate($this->validPayload(['title' => str_repeat('a', 101)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('title', $v->errors()->toArray());
    }

    public function test_description_wajib_diisi(): void
    {
        $v = $this->validate($this->validPayload(['description' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('description', $v->errors()->toArray());
    }

    public function test_type_wajib_diisi(): void
    {
        $v = $this->validate($this->validPayload(['type' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('type', $v->errors()->toArray());
    }

    public function test_type_hanya_freelance_atau_part_time(): void
    {
        $v = $this->validate($this->validPayload(['type' => 'full-time']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('type', $v->errors()->toArray());
    }

    public function test_type_freelance_valid(): void
    {
        $v = $this->validate($this->validPayload(['type' => 'freelancer']));
        $this->assertFalse($v->fails());
    }

    public function test_type_part_time_valid(): void
    {
        $v = $this->validate($this->validPayload(['type' => 'parttime']));
        $this->assertFalse($v->fails());
    }

    public function test_status_wajib_diisi(): void
    {
        $v = $this->validate($this->validPayload(['status' => '']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('status', $v->errors()->toArray());
    }

    public function test_status_hanya_nilai_enum_valid(): void
    {
        $v = $this->validate($this->validPayload(['status' => 'pending']));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('status', $v->errors()->toArray());
    }

    public function test_status_draft_valid(): void
    {
        $v = $this->validate($this->validPayload(['status' => 'draft']));
        $this->assertFalse($v->fails());
    }

    public function test_status_published_valid(): void
    {
        $v = $this->validate($this->validPayload(['status' => 'published']));
        $this->assertFalse($v->fails());
    }

    public function test_requirements_nullable(): void
    {
        $v = $this->validate($this->validPayload(['requirements' => null]));
        $this->assertFalse($v->fails());
    }

    public function test_salary_range_nullable(): void
    {
        $v = $this->validate($this->validPayload(['salary_range' => null]));
        $this->assertFalse($v->fails());
    }

    public function test_salary_range_maksimal_100_karakter(): void
    {
        $v = $this->validate($this->validPayload(['salary_range' => str_repeat('a', 101)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('salary_range', $v->errors()->toArray());
    }

    public function test_location_nullable(): void
    {
        $v = $this->validate($this->validPayload(['location' => null]));
        $this->assertFalse($v->fails());
    }

    public function test_location_maksimal_150_karakter(): void
    {
        $v = $this->validate($this->validPayload(['location' => str_repeat('a', 151)]));
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('location', $v->errors()->toArray());
    }

    public function test_payload_lengkap_valid(): void
    {
        $v = $this->validate([
            'title'        => 'Backend Developer Laravel TEST',
            'description'  => 'Deskripsi lengkap pekerjaan TEST.',
            'requirements' => 'Minimal 2 tahun pengalaman TEST.',
            'salary_range' => '5.000.000 - 8.000.000',
            'location'     => 'Jakarta Selatan TEST',
            'type'         => 'freelancer',
            'status'       => 'published',
        ]);
        $this->assertFalse($v->fails());
    }
}
