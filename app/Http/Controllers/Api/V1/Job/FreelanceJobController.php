<?php

namespace App\Http\Controllers\Api\V1\Job;

use App\DTOs\Job\JobFilterDataDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Job\JobCollection;
use App\Http\Resources\Api\V1\Job\JobResource;
use App\Services\Job\JobService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreelanceJobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly JobService $jobService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filter = JobFilterDataDTO::from([
            'search'   => $request->query('filter.search') ?? $request->query('search'),
            'location' => $request->query('filter.location') ?? $request->query('location'),
            'type'     => $request->query('filter.type') ?? $request->query('type'),
            'per_page' => (int) $request->query('per_page', 15),
        ]);

        $jobs = $this->jobService->getPublishedJobs($filter);

        return $this->paginated(
            new JobCollection($jobs),
            'Daftar lowongan berhasil diambil.',
        );
    }

    public function show(string $id): JsonResponse
    {
        $job = $this->jobService->getPublishedJob($id);

        return $this->ok([
            'job' => new JobResource($job),
        ]);
    }
}
