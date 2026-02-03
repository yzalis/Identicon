<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Value;

use Yzalis\Identicon\Exception\InvalidGridSizeException;

final readonly class GridSize
{
    public const MIN = 3;
    public const MAX = 25;
    public const DEFAULT = 5;

    public function __construct(
        public int $columns,
        public int $rows,
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

    public static function forImageSize(int $pixels): self
    {
        $optimalCellSize = 60;
        $gridSize = (int) round($pixels / $optimalCellSize);

        $gridSize = max(self::MIN, min(self::MAX, $gridSize));

        if ($gridSize % 2 === 0) {
            $gridSize++;
        }

        return self::square($gridSize);
    }

    public function isSquare(): bool
    {
        return $this->columns === $this->rows;
    }

    public function getTotalCells(): int
    {
        return $this->columns * $this->rows;
    }

    public function getHalfColumns(): int
    {
        return (int) ceil($this->columns / 2);
    }

    public function getRequiredBytes(): int
    {
        return $this->getHalfColumns() * $this->rows;
    }

    public function hasMiddleColumn(): bool
    {
        return $this->columns % 2 === 1;
    }

    private function validate(): void
    {
        if ($this->columns < self::MIN || $this->columns > self::MAX) {
            throw InvalidGridSizeException::columnsOutOfRange($this->columns, self::MIN, self::MAX);
        }

        if ($this->rows < self::MIN || $this->rows > self::MAX) {
            throw InvalidGridSizeException::rowsOutOfRange($this->rows, self::MIN, self::MAX);
        }
    }
}
