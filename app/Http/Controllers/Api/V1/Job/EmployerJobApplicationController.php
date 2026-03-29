<?php

namespace App\Http\Controllers\Api\V1\Job;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Job\JobApplicationCollection;
use App\Http\Resources\Api\V1\Job\JobApplicationResource;
use App\Services\Job\JobApplicationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployerJobApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly JobApplicationService $jobApplicationService,
    ) {}

    public function index(Request $request, string $jobId): JsonResponse
    {
        $applications = $this->jobApplicationService->getJobApplications(
            employer: $request->user(),
            jobId:    $jobId,
            perPage:  (int) $request->query('per_page', 15),
        );

        return $this->paginated(
            new JobApplicationCollection($applications),
            'Daftar pelamar berhasil diambil.',
        );
    }

    public function show(Request $request, string $applicationId): JsonResponse
    {
        $application = $this->jobApplicationService->getApplicationDetail(
            employer:       $request->user(),
            applicationId: $applicationId,
        );

        return $this->ok([
            'application' => new JobApplicationResource($application),
        ]);
    }

    public function downloadCv(Request $request, string $applicationId): BinaryFileResponse
    {
        $cv = $this->jobApplicationService->getCvForDownload(
            employer:      $request->user(),
            applicationId: $applicationId,
        );

        return response()->download(
            file:    $cv['path'],
            name:    $cv['original_name'],
            headers: ['Content-Type' => $cv['mime_type']],
        );
    }
}
