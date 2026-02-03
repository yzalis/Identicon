<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\Hash;

/**
 * Improved color extraction using HSL for better distribution.
 * Ensures colors are vibrant and visible (not too dark or light).
 */
final class HslColorExtractor implements ColorExtractorInterface
{
    private const MIN_SATURATION = 0.45;
    private const MAX_SATURATION = 0.75;
    private const MIN_LIGHTNESS = 0.40;
    private const MAX_LIGHTNESS = 0.60;

    public function extractColor(Hash $hash): Color
    {
        $bytes = $hash->getBytes();
        $length = \strlen($bytes);

        $byte1 = \ord($bytes[$length - 3]);
        $byte2 = \ord($bytes[$length - 2]);
        $byte3 = \ord($bytes[$length - 1]);

        $hue = (($byte1 << 8) | $byte2) % 360;

        $saturationRange = self::MAX_SATURATION - self::MIN_SATURATION;
        $saturation = self::MIN_SATURATION + ($byte3 / 255) * $saturationRange;

        $byte4 = $length >= 4 ? \ord($bytes[$length - 4]) : $byte1;
        $lightnessRange = self::MAX_LIGHTNESS - self::MIN_LIGHTNESS;
        $lightness = self::MIN_LIGHTNESS + ($byte4 / 255) * $lightnessRange;

        return $this->hslToRgb($hue, $saturation, $lightness);
    }

    private function hslToRgb(int $h, float $s, float $l): Color
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        $r = 0.0;
        $g = 0.0;
        $b = 0.0;

        if ($h < 60) {
            $r = $c;
            $g = $x;
        } elseif ($h < 120) {
            $r = $x;
            $g = $c;
        } elseif ($h < 180) {
            $g = $c;
            $b = $x;
        } elseif ($h < 240) {
            $g = $x;
            $b = $c;
        } elseif ($h < 300) {
            $r = $x;
            $b = $c;
        } else {
            $r = $c;
            $b = $x;
        }

        return new Color(
            (int) round(($r + $m) * 255),
            (int) round(($g + $m) * 255),
            (int) round(($b + $m) * 255),
        );
    }
}
