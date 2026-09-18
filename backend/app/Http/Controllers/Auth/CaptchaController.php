<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;

class CaptchaController extends Controller
{
    /**
     * Generate a new captcha challenge.
     */
    public function show(CaptchaService $captchaService): JsonResponse
    {
        $captcha = $captchaService->generate();

        return response()->json([
            'success' => true,
            'data' => $captcha,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
