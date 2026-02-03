<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Value;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Exception\InvalidColorException;
use Yzalis\Identicon\Value\Color;

final class ColorTest extends TestCase
{
    #[DataProvider('validHexColorProvider')]
    public function testFromHex(string $hex, int $red, int $green, int $blue): void
    {
        $color = Color::fromHex($hex);

        self::assertSame($red, $color->red);
        self::assertSame($green, $color->green);
        self::assertSame($blue, $color->blue);
        self::assertSame(255, $color->alpha);
    }

    public static function validHexColorProvider(): iterable
    {
        yield 'full hex with hash' => ['#ffffff', 255, 255, 255];
        yield 'full hex without hash' => ['000000', 0, 0, 0];
        yield 'short hex with hash' => ['#fff', 255, 255, 255];
        yield 'short hex without hash' => ['f0f', 255, 0, 255];
        yield 'mixed case' => ['#AbCdEf', 171, 205, 239];
        yield 'specific color' => ['#3498db', 52, 152, 219];
    }

    #[DataProvider('invalidHexColorProvider')]
    public function testFromHexThrowsOnInvalidInput(string $hex): void
    {
        $this->expectException(InvalidColorException::class);
        Color::fromHex($hex);
    }

    public static function invalidHexColorProvider(): iterable
    {
        yield 'invalid chars' => ['gggggg'];
        yield 'too short' => ['ff'];
        yield 'too long' => ['fffffff'];
        yield 'wrong length' => ['fffff'];
    }

    public function testFromArray(): void
    {
        $color = Color::fromArray([128, 64, 32]);

        self::assertSame(128, $color->red);
        self::assertSame(64, $color->green);
        self::assertSame(32, $color->blue);
        self::assertSame(255, $color->alpha);
    }

    public function testFromArrayWithAlpha(): void
    {
        $color = Color::fromArray([128, 64, 32, 100]);

        self::assertSame(100, $color->alpha);
    }

    public function testFromArrayWithNamedKeys(): void
    {
        $color = Color::fromArray(['r' => 100, 'g' => 150, 'b' => 200]);

        self::assertSame(100, $color->red);
        self::assertSame(150, $color->green);
        self::assertSame(200, $color->blue);
    }

    public function testToHex(): void
    {
        $color = new Color(171, 205, 239);

        self::assertSame('#ABCDEF', $color->toHex());
        self::assertSame('#abcdef', $color->toHexLower());
    }

    public function testToRgbString(): void
    {
        $color = new Color(100, 150, 200);

        self::assertSame('rgb(100, 150, 200)', $color->toRgbString());
    }

    public function testToRgbaString(): void
    {
        $color = new Color(100, 150, 200, 128);

        self::assertSame('rgba(100, 150, 200, 0.50)', $color->toRgbString());
    }

    public function testContrastRatio(): void
    {
        $white = Color::white();
        $black = Color::black();

        $ratio = $white->getContrastRatio($black);

        self::assertEqualsWithDelta(21.0, $ratio, 0.1);
    }

    #[DataProvider('outOfRangeColorProvider')]
    public function testRejectsOutOfRangeValues(int $r, int $g, int $b): void
    {
        $this->expectException(InvalidColorException::class);
        new Color($r, $g, $b);
    }

    public static function outOfRangeColorProvider(): iterable
    {
        yield 'negative red' => [-1, 0, 0];
        yield 'negative green' => [0, -1, 0];
        yield 'negative blue' => [0, 0, -1];
        yield 'red over 255' => [256, 0, 0];
        yield 'green over 255' => [0, 256, 0];
        yield 'blue over 255' => [0, 0, 256];
    }

    public function testTransparent(): void
    {
        $color = Color::transparent();

        self::assertTrue($color->isTransparent());
        self::assertSame(0, $color->alpha);
    }

    public function testWithAlpha(): void
    {
        $color = new Color(100, 100, 100);
        $withAlpha = $color->withAlpha(50);

        self::assertSame(255, $color->alpha);
        self::assertSame(50, $withAlpha->alpha);
    }
}
