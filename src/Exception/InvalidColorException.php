<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Exception;

final class InvalidColorException extends IdenticonException
{
    public static function invalidHex(string $hex): self
    {
        return new self(\sprintf(
            'Invalid hexadecimal color: "%s". Expected format: #RGB, #RRGGBB, RGB, or RRGGBB.',
            $hex,
        ));
    }

    public static function outOfRange(string $component, int $value): self
    {
        return new self(\sprintf(
            'Color component "%s" value %d is out of range. Expected 0-255.',
            $component,
            $value,
        ));
    }

    public static function invalidArray(): self
    {
        return new self(
            'Invalid color array. Expected [r, g, b] or [r, g, b, a] or ["r" => r, "g" => g, "b" => b].',
        );
    }
}
