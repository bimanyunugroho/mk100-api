<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\Job\ApplyJobRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApplyJobRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function validate(array $data, array $files = []): \Illuminate\Validation\Validator
    {
        return Validator::make(
            array_merge($data, $files),
            (new ApplyJobRequest())->rules()
        );
    }

    public function test_cv_file_wajib_ada(): void
    {
        $v = $this->validate([]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('cv_file', $v->errors()->toArray());
    }

    public function test_cv_file_pdf_valid(): void
    {
        $v = $this->validate([
            'cv_file' => UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'),
        ]);
        $this->assertFalse($v->fails());
    }

    public function test_cv_file_format_image_ditolak(): void
    {
        $v = $this->validate([
            'cv_file' => UploadedFile::fake()->create('foto.jpg', 500, 'image/jpeg'),
        ]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('cv_file', $v->errors()->toArray());
    }

    public function test_cv_file_format_txt_ditolak(): void
    {
        $v = $this->validate([
            'cv_file' => UploadedFile::fake()->create('cv.txt', 100, 'text/plain'),
        ]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('cv_file', $v->errors()->toArray());
    }

    public function test_cv_file_melebihi_5mb_ditolak(): void
    {
        $v = $this->validate([
            'cv_file' => UploadedFile::fake()->create('cv.pdf', 5121, 'application/pdf'),
        ]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('cv_file', $v->errors()->toArray());
    }

    public function test_cv_file_tepat_5mb_valid(): void
    {
        $v = $this->validate([
            'cv_file' => UploadedFile::fake()->create('cv.pdf', 5120, 'application/pdf'),
        ]);
        $this->assertFalse($v->fails());
    }

    public function test_cover_letter_nullable(): void
    {
        $v = $this->validate([
            'cv_file'      => UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'),
            'cover_letter' => null,
        ]);
        $this->assertFalse($v->fails());
    }

    public function test_cover_letter_maksimal_500_karakter(): void
    {
        $v = $this->validate([
            'cv_file'      => UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'),
            'cover_letter' => str_repeat('a', 501),
        ]);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('cover_letter', $v->errors()->toArray());
    }

    public function test_payload_dengan_cover_letter_valid(): void
    {
        $v = $this->validate([
            'cv_file'      => UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'),
            'cover_letter' => 'Saya sangat tertarik dengan posisi ini.',
        ]);
        $this->assertFalse($v->fails());
    }
}
