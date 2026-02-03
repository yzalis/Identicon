<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\IdenticonBuilder;

final class BuilderConfigTest extends TestCase
{
    #[DataProvider('sizeProvider')]
    public function testDifferentSizes(int $size): void
    {
        $identicon = (new IdenticonBuilder())
            ->size($size)
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        self::assertStringContainsString("width=\"{$size}\"", $data);
        self::assertStringContainsString("height=\"{$size}\"", $data);
    }

    public static function sizeProvider(): iterable
    {
        yield 'tiny' => [32];
        yield 'small' => [64];
        yield 'medium' => [128];
        yield 'large' => [256];
        yield 'xlarge' => [512];
        yield 'xxlarge' => [1024];
    }

    #[DataProvider('marginPixelProvider')]
    public function testMarginInPixels(int $margin): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(100)
            ->margin($margin)
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame($margin, $config->margin->top);
        self::assertSame($margin, $config->margin->right);
        self::assertSame($margin, $config->margin->bottom);
        self::assertSame($margin, $config->margin->left);
    }

    public static function marginPixelProvider(): iterable
    {
        yield 'none' => [0];
        yield 'small' => [5];
        yield 'medium' => [10];
        yield 'large' => [20];
    }

    public function testMarginPercent(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(200)
            ->marginPercent(10) // 10% of 200 = 20px
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame(20, $config->margin->top);
        self::assertSame(20, $config->margin->right);
        self::assertSame(20, $config->margin->bottom);
        self::assertSame(20, $config->margin->left);
    }

    public function testMarginPercentLargeImage(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(420)
            ->marginPercent(10) // 10% of 420 = 42px
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame(42, $config->margin->top);
    }

    public function testAsymmetricMargin(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(100)
            ->margin([5, 10, 15, 20]) // top, right, bottom, left
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame(5, $config->margin->top);
        self::assertSame(10, $config->margin->right);
        self::assertSame(15, $config->margin->bottom);
        self::assertSame(20, $config->margin->left);
    }

    #[DataProvider('colorFormatProvider')]
    public function testColorFormats(string|array $color, string $expectedHex): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->color($color)
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        self::assertStringContainsString($expectedHex, $data);
    }

    public static function colorFormatProvider(): iterable
    {
        yield 'hex-full' => ['#3498db', '#3498db'];
        yield 'hex-no-hash' => ['3498db', '#3498db'];
        yield 'hex-short' => ['#fff', '#ffffff'];
        yield 'hex-short-no-hash' => ['f00', '#ff0000'];
        yield 'rgb-array' => [[52, 152, 219], '#3498db'];
    }

    #[DataProvider('backgroundColorProvider')]
    public function testBackgroundColors(string|array|null $bgColor, ?string $expectedInSvg): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->backgroundColor($bgColor)
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        if ($expectedInSvg !== null) {
            self::assertStringContainsString($expectedInSvg, $data);
        } else {
            // Transparent background should not have a background rect
            self::assertStringNotContainsString('<rect width="64" height="64"', $data);
        }
    }

    public static function backgroundColorProvider(): iterable
    {
        yield 'white' => ['#ffffff', '#ffffff'];
        yield 'black' => ['#000000', '#000000'];
        yield 'transparent' => [null, null];
    }

    #[DataProvider('gridSizeProvider')]
    public function testGridSizes(int $gridSize): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(256)
            ->gridSize($gridSize)
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame($gridSize, $config->gridSize->columns);
        self::assertSame($gridSize, $config->gridSize->rows);
    }

    public static function gridSizeProvider(): iterable
    {
        yield 'minimum' => [3];
        yield 'default' => [5];
        yield 'medium' => [7];
        yield 'large' => [11];
        yield 'xlarge' => [15];
        yield 'maximum' => [25];
    }

    public function testCombinedConfiguration(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(512)
            ->gridSize(9)
            ->margin(32)
            ->color('#e74c3c')
            ->backgroundColor('#2c3e50')
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('complex-test');

        self::assertStringContainsString('width="512"', $data);
        self::assertStringContainsString('height="512"', $data);
        self::assertStringContainsString('#e74c3c', $data);
        self::assertStringContainsString('#2c3e50', $data);
    }

    public function testGdRendererWithMarginAndBackground(): void
    {
        if (!\extension_loaded('gd')) {
            self::markTestSkipped('GD extension not available');
        }

        $identicon = (new IdenticonBuilder())
            ->size(128)
            ->margin(16)
            ->backgroundColor('#ffffff')
            ->useGd()
            ->build();

        $data = $identicon->getImageData('gd-test');

        // Verify it's a valid PNG
        self::assertStringStartsWith("\x89PNG", $data);
    }

    public function testGdRendererLargeImageWithGridSize(): void
    {
        if (!\extension_loaded('gd')) {
            self::markTestSkipped('GD extension not available');
        }

        $identicon = (new IdenticonBuilder())
            ->size(512)
            ->gridSize(11)
            ->margin(24)
            ->color('#9b59b6')
            ->useGd()
            ->build();

        $data = $identicon->getImageData('large-gd-test');

        self::assertStringStartsWith("\x89PNG", $data);

        // Verify image dimensions
        $image = imagecreatefromstring($data);
        self::assertNotFalse($image);
        self::assertSame(512, imagesx($image));
        self::assertSame(512, imagesy($image));
    }
}
