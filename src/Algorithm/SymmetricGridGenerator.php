<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Grid\Cell;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\GridSize;
use Yzalis\Identicon\Value\Hash;

/**
 * Generates a horizontally symmetric grid pattern from a hash.
 * Ensures at least 25% of cells are filled to avoid empty identicons.
 */
final class SymmetricGridGenerator implements GridGeneratorInterface
{
    private const MIN_FILL_RATIO = 0.25;

    public function generate(Hash $hash, GridSize $gridSize): Grid
    {
        $bytes = $hash->getBytes();
        $halfCols = $gridSize->getHalfColumns();
        $hasMiddle = $gridSize->hasMiddleColumn();

        $cells = [];
        $filledCount = 0;

        for ($row = 0; $row < $gridSize->rows; $row++) {
            for ($col = 0; $col < $halfCols; $col++) {
                $byteIndex = ($row * $halfCols + $col) % \strlen($bytes);
                $byte = \ord($bytes[$byteIndex]);

                $filled = $byte >= 128;

                if ($filled) {
                    $filledCount++;
                }

                $cells[] = new Cell($col, $row, $filled);

                $mirrorCol = $gridSize->columns - 1 - $col;
                if ($mirrorCol !== $col) {
                    $cells[] = new Cell($mirrorCol, $row, $filled);
                    if ($filled) {
                        $filledCount++;
                    }
                }
            }
        }

        $totalCells = $gridSize->getTotalCells();
        $minRequired = (int) ceil($totalCells * self::MIN_FILL_RATIO);

        if ($filledCount < $minRequired) {
            $cells = $this->ensureMinimumFill($cells, $bytes, $gridSize, $filledCount, $minRequired);
        }

        usort(
            $cells,
            static fn (Cell $a, Cell $b): int =>
            $a->y === $b->y ? $a->x <=> $b->x : $a->y <=> $b->y,
        );

        return new Grid($gridSize, $cells);
    }

    /**
     * @param array<Cell> $cells
     *
     * @return array<Cell>
     */
    private function ensureMinimumFill(
        array $cells,
        string $bytes,
        GridSize $gridSize,
        int $currentFilled,
        int $minRequired,
    ): array {
        $needed = $minRequired - $currentFilled;
        $emptyCells = [];

        foreach ($cells as $index => $cell) {
            if (!$cell->filled) {
                $emptyCells[] = $index;
            }
        }

        $byteIndex = 0;
        $halfCols = $gridSize->getHalfColumns();

        foreach ($emptyCells as $cellIndex) {
            if ($needed <= 0) {
                break;
            }

            $cell = $cells[$cellIndex];

            if ($cell->x >= $halfCols) {
                continue;
            }

            $byte = \ord($bytes[$byteIndex % \strlen($bytes)]);
            $byteIndex++;

            if ($byte % 3 === 0) {
                $cells[$cellIndex] = new Cell($cell->x, $cell->y, true);
                $needed--;

                $mirrorCol = $gridSize->columns - 1 - $cell->x;
                if ($mirrorCol !== $cell->x) {
                    foreach ($cells as $i => $c) {
                        if ($c->x === $mirrorCol && $c->y === $cell->y && !$c->filled) {
                            $cells[$i] = new Cell($c->x, $c->y, true);
                            $needed--;
                            break;
                        }
                    }
                }
            }
        }

        return $cells;
    }
}
