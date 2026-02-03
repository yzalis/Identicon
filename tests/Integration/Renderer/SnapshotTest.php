<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Integration\Renderer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\IdenticonBuilder;

final class SnapshotTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/../../Fixtures/expected_outputs';

    #[DataProvider('svgSnapshotProvider')]
    public function testSvgSnapshot(string $input, string $expectedFile): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->useV2Compatibility()
            ->useSvg()
            ->build();

        $actual = $identicon->getImageData($input);
        $expectedPath = self::FIXTURES_DIR . '/' . $expectedFile;

        if (!file_exists($expectedPath)) {
            file_put_contents($expectedPath, $actual);
            self::markTestSkipped("Generated reference file: {$expectedFile}");
        }

        $expected = file_get_contents($expectedPath);
        self::assertSame($expected, $actual, "SVG output mismatch for input: {$input}");
    }

    public static function svgSnapshotProvider(): iterable
    {
        yield 'benjamin' => ['Benjamin', 'benjamin.svg'];
        yield 'email' => ['benjaminAtYzalisDotCom', 'email.svg'];
        yield 'ip1' => ['8.8.8.8', 'ip1.svg'];
        yield 'ip2' => ['8.8.4.4', 'ip2.svg'];
        yield 'yzalis' => ['yzalis', 'yzalis.svg'];
    }

    #[DataProvider('pngSnapshotProvider')]
    public function testPngSnapshot(string $input, string $expectedFile): void
    {
        if (!\extension_loaded('gd')) {
            self::markTestSkipped('GD extension not available');
        }

        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->useV2Compatibility()
            ->useGd()
            ->build();

        $actual = $identicon->getImageData($input);
        $expectedPath = self::FIXTURES_DIR . '/' . $expectedFile;

        if (!file_exists($expectedPath)) {
            file_put_contents($expectedPath, $actual);
            self::markTestSkipped("Generated reference file: {$expectedFile}");
        }

        $expected = file_get_contents($expectedPath);
        self::assertSame($expected, $actual, "PNG output mismatch for input: {$input}");
    }

    public static function pngSnapshotProvider(): iterable
    {
        yield 'benjamin' => ['Benjamin', 'benjamin.png'];
        yield 'email' => ['benjaminAtYzalisDotCom', 'email.png'];
        yield 'ip1' => ['8.8.8.8', 'ip1.png'];
        yield 'ip2' => ['8.8.4.4', 'ip2.png'];
        yield 'yzalis' => ['yzalis', 'yzalis.png'];
    }

    #[DataProvider('customConfigSnapshotProvider')]
    public function testCustomConfigSnapshot(
        string $input,
        int $size,
        int $gridSize,
        int $margin,
        string $expectedFile,
    ): void {
        $identicon = (new IdenticonBuilder())
            ->size($size)
            ->gridSize($gridSize)
            ->margin($margin)
            ->useSvg()
            ->build();

        $actual = $identicon->getImageData($input);
        $expectedPath = self::FIXTURES_DIR . '/' . $expectedFile;

        if (!file_exists($expectedPath)) {
            file_put_contents($expectedPath, $actual);
            self::markTestSkipped("Generated reference file: {$expectedFile}");
        }

        $expected = file_get_contents($expectedPath);
        self::assertSame($expected, $actual, "SVG output mismatch for: {$expectedFile}");
    }

    public static function customConfigSnapshotProvider(): iterable
    {
        yield 'large-grid' => ['test@example.com', 128, 7, 0, 'custom_7x7.svg'];
        yield 'with-margin' => ['test@example.com', 128, 5, 16, 'custom_margin.svg'];
        yield 'large-image' => ['test@example.com', 256, 9, 24, 'custom_large.svg'];
    }
}
