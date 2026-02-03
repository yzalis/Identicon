<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Grid;

use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Grid\Cell;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\GridSize;

final class GridTest extends TestCase
{
    public function testGetCells(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, false),
            new Cell(0, 1, true),
            new Cell(1, 1, true),
        ];

        $grid = new Grid(new GridSize(3, 3), $cells);

        self::assertCount(4, $grid->getCells());
    }

    public function testGetFilledCells(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, false),
            new Cell(0, 1, true),
            new Cell(1, 1, false),
        ];

        $grid = new Grid(GridSize::default(), $cells);
        $filled = $grid->getFilledCells();

        self::assertCount(2, $filled);
    }

    public function testGetCell(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, false),
            new Cell(2, 0, true),
        ];

        $grid = new Grid(GridSize::default(), $cells);

        $cell = $grid->getCell(1, 0);
        self::assertNotNull($cell);
        self::assertFalse($cell->filled);

        self::assertNull($grid->getCell(99, 99));
    }

    public function testIsFilled(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, false),
        ];

        $grid = new Grid(GridSize::default(), $cells);

        self::assertTrue($grid->isFilled(0, 0));
        self::assertFalse($grid->isFilled(1, 0));
        self::assertFalse($grid->isFilled(99, 99));
    }

    public function testGetFilledCount(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, true),
            new Cell(2, 0, false),
            new Cell(0, 1, true),
        ];

        $grid = new Grid(GridSize::default(), $cells);

        self::assertSame(3, $grid->getFilledCount());
    }

    public function testGetFillRatio(): void
    {
        $gridSize = GridSize::square(5); // 25 cells total

        $cells = [];
        for ($y = 0; $y < 5; $y++) {
            for ($x = 0; $x < 5; $x++) {
                $cells[] = new Cell($x, $y, $x < 2); // 2 per row = 10 total
            }
        }

        $grid = new Grid($gridSize, $cells);

        self::assertEqualsWithDelta(0.4, $grid->getFillRatio(), 0.01);
    }

    public function testToMatrix(): void
    {
        $cells = [
            new Cell(0, 0, true),
            new Cell(1, 0, false),
            new Cell(2, 0, true),
            new Cell(0, 1, false),
            new Cell(1, 1, true),
            new Cell(2, 1, false),
        ];

        $grid = new Grid(new GridSize(3, 3), $cells);
        $matrix = $grid->toMatrix();

        self::assertSame(true, $matrix[0][0]);
        self::assertSame(false, $matrix[0][1]);
        self::assertSame(true, $matrix[0][2]);
        self::assertSame(false, $matrix[1][0]);
        self::assertSame(true, $matrix[1][1]);
    }
}
