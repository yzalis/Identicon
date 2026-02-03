<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Grid;

use Yzalis\Identicon\Value\GridSize;

final readonly class Grid
{
    /**
     * @param array<Cell> $cells
     */
    public function __construct(
        public GridSize $gridSize,
        private array $cells,
    ) {
    }

    /**
     * @return array<Cell>
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @return array<Cell>
     */
    public function getFilledCells(): array
    {
        return array_filter($this->cells, static fn (Cell $cell): bool => $cell->filled);
    }

    public function getCell(int $x, int $y): ?Cell
    {
        foreach ($this->cells as $cell) {
            if ($cell->x === $x && $cell->y === $y) {
                return $cell;
            }
        }

        return null;
    }

    public function isFilled(int $x, int $y): bool
    {
        $cell = $this->getCell($x, $y);

        return $cell !== null && $cell->filled;
    }

    public function getFilledCount(): int
    {
        return \count($this->getFilledCells());
    }

    public function getFillRatio(): float
    {
        $total = $this->gridSize->getTotalCells();

        if ($total === 0) {
            return 0.0;
        }

        return $this->getFilledCount() / $total;
    }

    /**
     * @return array<array<bool>>
     */
    public function toMatrix(): array
    {
        $matrix = [];

        for ($y = 0; $y < $this->gridSize->rows; $y++) {
            $matrix[$y] = [];
            for ($x = 0; $x < $this->gridSize->columns; $x++) {
                $matrix[$y][$x] = $this->isFilled($x, $y);
            }
        }

        return $matrix;
    }
}
