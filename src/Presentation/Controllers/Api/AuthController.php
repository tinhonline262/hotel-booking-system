<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Services\AuthService;

/**
 * Auth Controller - Fixed Version
 */
class AuthController extends BaseRestController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * POST /api/auth/login
     * Admin login
     * 
     * FIX: Thêm kiểm tra $result['admin'] !== null
     */
    public function login(): void
    {
        try {
            $data = $this->getJsonInput();
            $result = $this->authService->login($data);

            // FIX: Thêm && $result['admin'] !== null
            if ($result['success'] && isset($result['admin']) && $result['admin'] !== null) {
                $this->success(
                    [
                        'admin' => $result['admin']->toArray()
                    ],
                    $result['message'] ?? 'Login successful'
                );
            } else {
                $this->validationError(
                    $result['errors'] ?? [],
                    $result['message'] ?? 'Login failed'
                );
            }
        } catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $this->serverError('Login failed');
        }
    }

    /**
     * POST /api/auth/logout
     * Admin logout
     */
    public function logout(): void
    {
        try {
            $result = $this->authService->logout();
            $this->success(null, $result['message']);
        } catch (\Exception $e) {
            error_log('Logout error: ' . $e->getMessage());
            $this->serverError('Logout failed');
        }
    }

    /**
     * GET /api/auth/check
     * Check if admin is authenticated
     */
    public function check(): void
    {
        try {
            $isAuthenticated = $this->authService->isAuthenticated();
            $admin = $isAuthenticated ? $this->authService->getCurrentAdmin() : null;

            $this->success(
                [
                    'authenticated' => $isAuthenticated,
                    'admin' => $admin
                ],
                $isAuthenticated ? 'Authenticated' : 'Not authenticated'
            );
        } catch (\Exception $e) {
            error_log('Auth check error: ' . $e->getMessage());
            $this->serverError('Failed to check authentication');
        }
    }
}