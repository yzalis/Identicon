<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Value;

use Yzalis\Identicon\Exception\InvalidSizeException;

final readonly class Size
{
    public const MIN = 16;
    public const MAX = 4096;
    public const DEFAULT = 64;

    public function __construct(
        public int $width,
        public int $height,
    ) {
        $this->validate();
    }

    public static function square(int $size): self
    {
        return new self($size, $size);
    }

    public static function default(): self
    {
        return self::square(self::DEFAULT);
    }

    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }

    public function getMinDimension(): int
    {
        return min($this->width, $this->height);
    }

    public function getMaxDimension(): int
    {
        return max($this->width, $this->height);
    }

    public function scale(float $factor): self
    {
        return new self(
            (int) round($this->width * $factor),
            (int) round($this->height * $factor),
        );
    }

    private function validate(): void
    {
        if ($this->width <= 0) {
            throw InvalidSizeException::notPositive($this->width);
        }

        if ($this->height <= 0) {
            throw InvalidSizeException::notPositive($this->height);
        }

        if ($this->width < self::MIN) {
            throw InvalidSizeException::tooSmall($this->width, self::MIN);
        }

        if ($this->height < self::MIN) {
            throw InvalidSizeException::tooSmall($this->height, self::MIN);
        }

        if ($this->width > self::MAX) {
            throw InvalidSizeException::tooLarge($this->width, self::MAX);
        }

        if ($this->height > self::MAX) {
            throw InvalidSizeException::tooLarge($this->height, self::MAX);
        }
    }
}
