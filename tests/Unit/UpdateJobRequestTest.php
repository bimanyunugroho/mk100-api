<?php

namespace Tests\Unit;


use App\Http\Requests\Api\V1\Job\UpdateJobRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateJobRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new UpdateJobRequest())->rules());
    }

    public function test_payload_kosong_lolos_validasi(): void
    {
        $v = $this->validate([]);
        $this->assertFalse($v->fails());
    }

    public function test_title_maksimal_100_jika_dikirim(): void
    {
        $v = $this->validate(['title' => str_repeat('a', 101)]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('title', $v->errors()->toArray());
    }

    public function test_title_valid_jika_dikirim(): void
    {
        $v = $this->validate(['title' => 'Judul Baru']);
        $this->assertFalse($v->fails());
    }

    public function test_type_hanya_enum_valid_jika_dikirim(): void
    {
        $v = $this->validate(['type' => 'full-time']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('type', $v->errors()->toArray());
    }

    public function test_type_freelance_valid(): void
    {
        $v = $this->validate(['type' => 'freelancer']);
        $this->assertFalse($v->fails());
    }

    public function test_type_part_time_valid(): void
    {
        $v = $this->validate(['type' => 'parttime']);
        $this->assertFalse($v->fails());
    }

    public function test_status_hanya_enum_valid_jika_dikirim(): void
    {
        $v = $this->validate(['status' => 'nonexistent']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('status', $v->errors()->toArray());
    }

    public function test_status_closed_valid_untuk_update(): void
    {
        $v = $this->validate(['status' => 'closed']);
        $this->assertFalse($v->fails());
    }

    public function test_status_published_valid(): void
    {
        $v = $this->validate(['status' => 'published']);
        $this->assertFalse($v->fails());
    }

    public function test_status_draft_valid(): void
    {
        $v = $this->validate(['status' => 'draft']);
        $this->assertFalse($v->fails());
    }

    public function test_salary_range_maksimal_100_jika_dikirim(): void
    {
        $v = $this->validate(['salary_range' => str_repeat('a', 101)]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('salary_range', $v->errors()->toArray());
    }

    public function test_location_maksimal_150_jika_dikirim(): void
    {
        $v = $this->validate(['location' => str_repeat('a', 151)]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('location', $v->errors()->toArray());
    }

    public function test_partial_update_valid(): void
    {
        $v = $this->validate([
            'title'  => 'Judul Diupdate',
            'status' => 'published',
        ]);
        $this->assertFalse($v->fails());
    }
}
