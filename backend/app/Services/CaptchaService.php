<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaService
{
    /**
     * Session key for storing captcha.
     */
    const SESSION_KEY = 'captcha.hash';

    const EXPIRY_KEY = 'captcha.expiry';

    /**
     * Generate a new captcha challenge.
     *
     * @return array Contains 'image' (base64 svg) and 'expires_in'
     */
    public function generate(): array
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length = 5;
        $code = '';

        // Cryptographically secure random string generation
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        // Store hash and expiry (5 minutes) in session
        $expiresAt = now()->addMinutes(5);
        Session::put(self::SESSION_KEY, Hash::make($code));
        Session::put(self::EXPIRY_KEY, $expiresAt->timestamp);

        return [
            'image' => $this->generateSvg($code),
            'expires_in' => 300,
        ];
    }

    /**
     * Validate the given code against the session.
     */
    public function validate(string $code): bool
    {
        $hash = Session::get(self::SESSION_KEY);
        $expiry = Session::get(self::EXPIRY_KEY);

        // Always consume the challenge after an attempt
        Session::forget([self::SESSION_KEY, self::EXPIRY_KEY]);

        if (! $hash || ! $expiry || now()->timestamp > $expiry) {
            return false;
        }

        $code = Str::upper(trim($code));

        return Hash::check($code, $hash);
    }

    /**
     * Generate an SVG image for the captcha code.
     *
     * @return string Data URI
     */
    protected function generateSvg(string $code): string
    {
        $width = 120;
        $height = 40;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">';

        // Background
        $svg .= '<rect width="100%" height="100%" fill="#f8fafc"/>';

        // Add some noise lines
        for ($i = 0; $i < 5; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#cbd5e1" stroke-width="2"/>';
        }

        // Draw each character with slight rotation and offset
        $charWidth = $width / strlen($code);
        for ($i = 0; $i < strlen($code); $i++) {
            $char = htmlspecialchars($code[$i], ENT_QUOTES, 'UTF-8');
            $rotation = random_int(-15, 15);
            $x = ($i * $charWidth) + ($charWidth / 2);
            $y = ($height / 2) + 8; // approx center vertically

            $svg .= '<text x="'.$x.'" y="'.$y.'" font-family="monospace" font-size="24" font-weight="bold" fill="#0f172a" text-anchor="middle" transform="rotate('.$rotation.' '.$x.' '.$y.')">'.$char.'</text>';
        }

        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
