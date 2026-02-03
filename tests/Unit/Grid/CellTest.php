<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Grid;

use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Grid\Cell;

final class CellTest extends TestCase
{
    public function testFilledCell(): void
    {
        $cell = new Cell(2, 3, true);

        self::assertSame(2, $cell->x);
        self::assertSame(3, $cell->y);
        self::assertTrue($cell->filled);
        self::assertFalse($cell->isEmpty());
    }

    public function testEmptyCell(): void
    {
        $cell = new Cell(0, 0, false);

        self::assertFalse($cell->filled);
        self::assertTrue($cell->isEmpty());
    }
}
