<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Value;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Exception\InvalidGridSizeException;
use Yzalis\Identicon\Value\GridSize;

final class GridSizeTest extends TestCase
{
    public function testDefault(): void
    {
        $gridSize = GridSize::default();

        self::assertSame(5, $gridSize->columns);
        self::assertSame(5, $gridSize->rows);
        self::assertTrue($gridSize->isSquare());
    }

    public function testSquare(): void
    {
        $gridSize = GridSize::square(7);

        self::assertSame(7, $gridSize->columns);
        self::assertSame(7, $gridSize->rows);
    }

    public function testTotalCells(): void
    {
        $gridSize = new GridSize(5, 7);

        self::assertSame(35, $gridSize->getTotalCells());
    }

    public function testHalfColumns(): void
    {
        self::assertSame(3, GridSize::square(5)->getHalfColumns());
        self::assertSame(3, GridSize::square(6)->getHalfColumns());
        self::assertSame(4, GridSize::square(7)->getHalfColumns());
    }

    public function testHasMiddleColumn(): void
    {
        self::assertTrue(GridSize::square(5)->hasMiddleColumn());
        self::assertFalse(GridSize::square(6)->hasMiddleColumn());
        self::assertTrue(GridSize::square(7)->hasMiddleColumn());
    }

    #[DataProvider('forImageSizeProvider')]
    public function testForImageSize(int $imageSize, int $expectedGrid): void
    {
        $gridSize = GridSize::forImageSize($imageSize);

        self::assertSame($expectedGrid, $gridSize->columns);
        self::assertTrue($gridSize->hasMiddleColumn());
    }

    public static function forImageSizeProvider(): iterable
    {
        yield '64px' => [64, 3];
        yield '128px' => [128, 3];
        yield '256px' => [256, 5];
        yield '512px' => [512, 9];
        yield '1024px' => [1024, 17];
        yield '2048px' => [2048, 25];
    }

    #[DataProvider('invalidGridSizeProvider')]
    public function testRejectsInvalidSize(int $columns, int $rows): void
    {
        $this->expectException(InvalidGridSizeException::class);
        new GridSize($columns, $rows);
    }

    public static function invalidGridSizeProvider(): iterable
    {
        yield 'columns too small' => [2, 5];
        yield 'rows too small' => [5, 2];
        yield 'columns too large' => [26, 5];
        yield 'rows too large' => [5, 26];
    }
}
