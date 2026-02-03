<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\Hash;

/**
 * Color extractor compatible with v2 algorithm.
 * Uses the last 3 hex characters of the hash multiplied by 16.
 */
final class LegacyColorExtractor implements ColorExtractorInterface
{
    public function extractColor(Hash $hash): Color
    {
        $hex = $hash->hex;
        $length = \strlen($hex);

        $r = hexdec($hex[$length - 3]) * 16;
        $g = hexdec($hex[$length - 2]) * 16;
        $b = hexdec($hex[$length - 1]) * 16;

        return new Color(
            min(255, (int) $r),
            min(255, (int) $g),
            min(255, (int) $b),
        );
    }
}
