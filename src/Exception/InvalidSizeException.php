<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Exception;

final class InvalidSizeException extends IdenticonException
{
    public static function tooSmall(int $size, int $minimum): self
    {
        return new self(\sprintf(
            'Size %d is too small. Minimum allowed: %d.',
            $size,
            $minimum,
        ));
    }

    public static function tooLarge(int $size, int $maximum): self
    {
        return new self(\sprintf(
            'Size %d is too large. Maximum allowed: %d.',
            $size,
            $maximum,
        ));
    }

    public static function notPositive(int $size): self
    {
        return new self(\sprintf(
            'Size must be a positive integer. Got: %d.',
            $size,
        ));
    }
}
