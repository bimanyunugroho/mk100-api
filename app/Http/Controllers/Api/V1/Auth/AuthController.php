<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTOs\Auth\LoginDataDTO;
use App\DTOs\Auth\RegisterDataDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginFormRequest;
use App\Http\Requests\Api\V1\Auth\RegisterFormRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(RegisterFormRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            RegisterDataDTO::from($request->validated()),
        );

        return $this->created([
            'user'       => new UserResource($result['user']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Registrasi berhasil.');
    }

    public function login(LoginFormRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            LoginDataDTO::from($request->validated()),
        );

        return $this->ok([
            'user'       => new UserResource($result['user']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Login berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->ok([
            'user' => new UserResource(
                $this->authService->me($request->user()),
            ),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->noContent('Logout berhasil.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return $this->noContent('Logout dari semua perangkat berhasil.');
    }
}
