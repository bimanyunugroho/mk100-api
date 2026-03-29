<?php

namespace App\Http\Controllers\Api\V1\Job;

use App\DTOs\Job\CreateJobDataDTO;
use App\DTOs\Job\UpdateJobDataDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Job\StoreJobRequest;
use App\Http\Requests\Api\V1\Job\UpdateJobRequest;
use App\Http\Resources\Api\V1\Job\JobCollection;
use App\Http\Resources\Api\V1\Job\JobResource;
use App\Services\Job\JobService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployerJobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly JobService $jobService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $jobs = $this->jobService->getEmployerJobs(
            employer: $request->user(),
            perPage:  (int) $request->query('per_page', 15),
        );

        return $this->paginated(
            new JobCollection($jobs),
            'Daftar job berhasil diambil.',
        );
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $job = $this->jobService->getEmployerJob($request->user(), $id);

        return $this->ok(
            ['job' => new JobResource($job)],
        );
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $job = $this->jobService->create(
            employer: $request->user(),
            createJobDataDTO:     CreateJobDataDTO::from($request->validated()),
        );

        return $this->created(
            ['job' => new JobResource($job)],
            'Job berhasil dibuat.',
        );
    }

    public function update(UpdateJobRequest $request, string $id): JsonResponse
    {
        $job = $this->jobService->update(
            employer: $request->user(),
            jobId:    $id,
            updateJobDataDTO:     UpdateJobDataDTO::from($request->validated()),
        );

        return $this->ok(
            ['job' => new JobResource($job)],
            'Job berhasil diperbarui.',
        );
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->jobService->delete($request->user(), $id);

        return $this->noContent('Job berhasil dihapus.');
    }
}
