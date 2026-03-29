<?php

namespace Tests\Unit;


use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Http\Resources\Api\V1\Job\JobApplicationCollection;
use App\Http\Resources\Api\V1\Job\JobApplicationResource;
use App\Http\Resources\Api\V1\Job\JobCollection;
use App\Http\Resources\Api\V1\Job\JobResource;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = Request::create('/');
    }

    public function test_user_resource_mengandung_field_yang_benar(): void
    {
        $user = $this->createEmployer();

        $resource = (new UserResource($user))->toArray($this->request);

        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('name', $resource);
        $this->assertArrayHasKey('email', $resource);
        $this->assertArrayHasKey('role', $resource);
        $this->assertArrayHasKey('company_name', $resource);
        $this->assertArrayHasKey('phone', $resource);
        $this->assertArrayHasKey('created_at', $resource);
    }

    public function test_user_resource_tidak_mengandung_password(): void
    {
        $user     = $this->createEmployer();
        $resource = (new UserResource($user))->toArray($this->request);

        $this->assertArrayNotHasKey('password', $resource);
        $this->assertArrayNotHasKey('remember_token', $resource);
    }

    public function test_user_resource_role_dalam_bentuk_string_value(): void
    {
        $employer   = $this->createEmployer();
        $freelancer = $this->createFreelancer();

        $employerResource   = (new UserResource($employer))->toArray($this->request);
        $freelancerResource = (new UserResource($freelancer))->toArray($this->request);

        $this->assertEquals('employer', $employerResource['role']);
        $this->assertEquals('freelancer', $freelancerResource['role']);
    }

    public function test_job_resource_mengandung_field_yang_benar(): void
    {
        $employer = $this->createEmployer();
        $job      = Job::factory()->forEmployer($employer)->published()->create();

        $resource = (new JobResource($job))->toArray($this->request);

        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('title', $resource);
        $this->assertArrayHasKey('description', $resource);
        $this->assertArrayHasKey('requirements', $resource);
        $this->assertArrayHasKey('salary_range', $resource);
        $this->assertArrayHasKey('location', $resource);
        $this->assertArrayHasKey('type', $resource);
        $this->assertArrayHasKey('status', $resource);
        $this->assertArrayHasKey('published_at', $resource);
        $this->assertArrayHasKey('created_at', $resource);
        $this->assertArrayHasKey('updated_at', $resource);
    }

    public function test_job_resource_type_dan_status_dalam_bentuk_string_value(): void
    {
        $employer = $this->createEmployer();
        $job      = Job::factory()->forEmployer($employer)->published()->freelance()->create();

        $resource = (new JobResource($job))->toArray($this->request);

        $this->assertIsString($resource['type']);
        $this->assertIsString($resource['status']);
        $this->assertEquals('freelancer', $resource['type']);
        $this->assertEquals('published', $resource['status']);
    }

    public function test_job_resource_employer_muncul_saat_diload(): void
    {
        $employer = $this->createEmployer();
        $job      = Job::factory()->forEmployer($employer)->published()->create();
        $job->load('employer');

        $response = response()->json(new JobResource($job))->getData(true);

        $this->assertArrayHasKey('employer', $response);
        $this->assertIsArray($response['employer']);
        $this->assertEquals($employer->id, $response['employer']['id']);
    }

    public function test_job_resource_published_at_null_untuk_draft(): void
    {
        $employer = $this->createEmployer();
        $job      = Job::factory()->forEmployer($employer)->draft()->create();

        $resource = (new JobResource($job))->toArray($this->request);

        $this->assertNull($resource['published_at']);
    }

    public function test_job_resource_published_at_tidak_null_untuk_published(): void
    {
        $employer = $this->createEmployer();
        $job      = Job::factory()->forEmployer($employer)->published()->create();

        $resource = (new JobResource($job))->toArray($this->request);

        $this->assertNotNull($resource['published_at']);
    }

    public function test_job_collection_wraps_multiple_jobs(): void
    {
        $employer = $this->createEmployer();
        $jobs     = Job::factory()->forEmployer($employer)->published()->count(3)->create();

        $paginator  = new LengthAwarePaginator($jobs, 3, 10, 1);
        $collection = new JobCollection($paginator);

        $response = $collection->toArray($this->request);

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(3, $response['data']);
    }

    public function test_job_collection_menggunakan_job_resource(): void
    {
        $employer = $this->createEmployer();
        $jobs     = Job::factory()->forEmployer($employer)->published()->count(2)->create();

        $paginator  = new LengthAwarePaginator($jobs, 2, 10, 1);
        $collection = new JobCollection($paginator);

        $this->assertEquals(JobResource::class, $collection->collects);
    }

    public function test_application_resource_mengandung_field_yang_benar(): void
    {
        $employer   = $this->createEmployer();
        $freelancer = $this->createFreelancer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();
        $application = JobApplication::factory()
            ->forJobAndFreelancer($job, $freelancer)
            ->create();

        $resource = (new JobApplicationResource($application))->toArray($this->request);

        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('cover_letter', $resource);
        $this->assertArrayHasKey('cv_original_name', $resource);
        $this->assertArrayHasKey('cv_mime_type', $resource);
        $this->assertArrayHasKey('cv_size_human', $resource);
        $this->assertArrayHasKey('created_at', $resource);
    }

    public function test_application_resource_tidak_expose_cv_path(): void
    {
        $employer   = $this->createEmployer();
        $freelancer = $this->createFreelancer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();
        $application = JobApplication::factory()
            ->forJobAndFreelancer($job, $freelancer)
            ->create();

        $resource = (new JobApplicationResource($application))->toArray($this->request);

        // cv_path ini path internal storage — ngga boleh bocor ke response
        $this->assertArrayNotHasKey('cv_path', $resource);
    }

    public function test_application_resource_freelancer_muncul_saat_diload(): void
    {
        $employer   = $this->createEmployer();
        $freelancer = $this->createFreelancer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();
        $application = JobApplication::factory()
            ->forJobAndFreelancer($job, $freelancer)
            ->create();
        $application->load('freelancer');

        $resource = (new JobApplicationResource($application))->toArray($this->request);

        $this->assertArrayHasKey('freelancer', $resource);
        $this->assertEquals($freelancer->id, $resource['freelancer']['id']);
    }

    public function test_application_resource_cv_size_human_format_benar(): void
    {
        $employer   = $this->createEmployer();
        $freelancer = $this->createFreelancer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();
        $application = JobApplication::factory()
            ->forJobAndFreelancer($job, $freelancer)
            ->create(['cv_size_bytes' => 1_048_576]); // 1MB

        $resource = (new JobApplicationResource($application))->toArray($this->request);

        $this->assertEquals('1 MB', $resource['cv_size_human']);
    }

    public function test_application_collection_wraps_multiple_applications(): void
    {
        $employer   = $this->createEmployer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();

        $freelancer1 = $this->createFreelancer();
        $freelancer2 = $this->createFreelancer();

        $applications = collect([
            JobApplication::factory()->forJobAndFreelancer($job, $freelancer1)->create(),
            JobApplication::factory()->forJobAndFreelancer($job, $freelancer2)->create(),
        ]);

        $paginator  = new LengthAwarePaginator($applications, 2, 10, 1);
        $collection = new JobApplicationCollection($paginator);

        $response = $collection->toArray($this->request);

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
    }

    public function test_application_collection_menggunakan_application_resource(): void
    {
        $employer   = $this->createEmployer();
        $job        = Job::factory()->forEmployer($employer)->published()->create();
        $freelancer = $this->createFreelancer();

        $applications = collect([
            JobApplication::factory()->forJobAndFreelancer($job, $freelancer)->create(),
        ]);

        $paginator  = new LengthAwarePaginator($applications, 1, 10, 1);
        $collection = new JobApplicationCollection($paginator);

        $this->assertEquals(JobApplicationResource::class, $collection->collects);
    }
}
