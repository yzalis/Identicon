<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Value;

use Yzalis\Identicon\Exception\InvalidColorException;

final readonly class Color
{
    public function __construct(
        public int $red,
        public int $green,
        public int $blue,
        public int $alpha = 255,
    ) {
        $this->validate();
    }

    public static function fromHex(string $hex): self
    {
        $hex = ltrim($hex, '#');

        if (\strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (!preg_match('/^[a-fA-F0-9]{6}$/', $hex)) {
            throw InvalidColorException::invalidHex($hex);
        }

        return new self(
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        );
    }

    /**
     * @param array<int|string, int> $rgb
     */
    public static function fromArray(array $rgb): self
    {
        $red = $rgb[0] ?? $rgb['r'] ?? $rgb['red'] ?? null;
        $green = $rgb[1] ?? $rgb['g'] ?? $rgb['green'] ?? null;
        $blue = $rgb[2] ?? $rgb['b'] ?? $rgb['blue'] ?? null;
        $alpha = $rgb[3] ?? $rgb['a'] ?? $rgb['alpha'] ?? 255;

        if ($red === null || $green === null || $blue === null) {
            throw InvalidColorException::invalidArray();
        }

        return new self((int) $red, (int) $green, (int) $blue, (int) $alpha);
    }

    /**
     * @param string|array<int|string, int> $color
     */
    public static function from(string|array $color): self
    {
        return \is_string($color) ? self::fromHex($color) : self::fromArray($color);
    }

    public static function transparent(): self
    {
        return new self(0, 0, 0, 0);
    }

    public static function white(): self
    {
        return new self(255, 255, 255);
    }

    public static function black(): self
    {
        return new self(0, 0, 0);
    }

    public function toHex(): string
    {
        return \sprintf('#%02X%02X%02X', $this->red, $this->green, $this->blue);
    }

    public function toHexLower(): string
    {
        return \sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
    }

    public function toRgbString(): string
    {
        if ($this->alpha === 255) {
            return \sprintf('rgb(%d, %d, %d)', $this->red, $this->green, $this->blue);
        }

        return \sprintf(
            'rgba(%d, %d, %d, %.2f)',
            $this->red,
            $this->green,
            $this->blue,
            $this->alpha / 255,
        );
    }

    /**
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    public function toArray(): array
    {
        return [$this->red, $this->green, $this->blue, $this->alpha];
    }

    public function isTransparent(): bool
    {
        return $this->alpha === 0;
    }

    public function withAlpha(int $alpha): self
    {
        return new self($this->red, $this->green, $this->blue, $alpha);
    }

    public function getLuminance(): float
    {
        $r = $this->red / 255;
        $g = $this->green / 255;
        $b = $this->blue / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : (($r + 0.055) / 1.055) ** 2.4;
        $g = $g <= 0.03928 ? $g / 12.92 : (($g + 0.055) / 1.055) ** 2.4;
        $b = $b <= 0.03928 ? $b / 12.92 : (($b + 0.055) / 1.055) ** 2.4;

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    public function getContrastRatio(Color $other): float
    {
        $l1 = $this->getLuminance();
        $l2 = $other->getLuminance();

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    private function validate(): void
    {
        $components = [
            'red' => $this->red,
            'green' => $this->green,
            'blue' => $this->blue,
            'alpha' => $this->alpha,
        ];

        foreach ($components as $name => $value) {
            if ($value < 0 || $value > 255) {
                throw InvalidColorException::outOfRange($name, $value);
            }
        }
    }
}
