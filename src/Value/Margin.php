<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Value;

use InvalidArgumentException;

final readonly class Margin
{
    public function __construct(
        public int $top,
        public int $right,
        public int $bottom,
        public int $left,
    ) {
        $this->validate();
    }

    public static function all(int $value): self
    {
        return new self($value, $value, $value, $value);
    }

    public static function none(): self
    {
        return new self(0, 0, 0, 0);
    }

    public static function symmetric(int $vertical, int $horizontal): self
    {
        return new self($vertical, $horizontal, $vertical, $horizontal);
    }

    public static function percent(int $imageSize, float $percent): self
    {
        if ($percent < 0 || $percent > 50) {
            throw new InvalidArgumentException(
                'Margin percentage must be between 0 and 50.',
            );
        }

        $margin = (int) round($imageSize * ($percent / 100));

        return self::all($margin);
    }

    /**
     * @param int|array<int|string, int> $margin
     */
    public static function from(int|array $margin): self
    {
        if (\is_int($margin)) {
            return self::all($margin);
        }

        if (isset($margin['top'], $margin['right'], $margin['bottom'], $margin['left'])) {
            return new self(
                (int) $margin['top'],
                (int) $margin['right'],
                (int) $margin['bottom'],
                (int) $margin['left'],
            );
        }

        return match (\count($margin)) {
            1 => self::all((int) ($margin[0] ?? 0)),
            2 => self::symmetric((int) ($margin[0] ?? 0), (int) ($margin[1] ?? 0)),
            4 => new self(
                (int) ($margin[0] ?? 0),
                (int) ($margin[1] ?? 0),
                (int) ($margin[2] ?? 0),
                (int) ($margin[3] ?? 0),
            ),
            default => throw new InvalidArgumentException(
                'Margin array must have 1, 2, or 4 elements.',
            ),
        };
    }

    public function getHorizontal(): int
    {
        return $this->left + $this->right;
    }

    public function getVertical(): int
    {
        return $this->top + $this->bottom;
    }

    public function isEmpty(): bool
    {
        return $this->top === 0
            && $this->right === 0
            && $this->bottom === 0
            && $this->left === 0;
    }

    public function isUniform(): bool
    {
        return $this->top === $this->right
            && $this->right === $this->bottom
            && $this->bottom === $this->left;
    }

    private function validate(): void
    {
        $sides = [
            'top' => $this->top,
            'right' => $this->right,
            'bottom' => $this->bottom,
            'left' => $this->left,
        ];

        foreach ($sides as $name => $value) {
            if ($value < 0) {
                throw new InvalidArgumentException(
                    \sprintf('Margin "%s" cannot be negative. Got: %d.', $name, $value),
                );
            }
        }
    }
}
