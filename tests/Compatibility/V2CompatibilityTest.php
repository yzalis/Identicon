<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Compatibility;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Algorithm\LegacyColorExtractor;
use Yzalis\Identicon\Algorithm\Md5HashAlgorithm;
use Yzalis\Identicon\Algorithm\SymmetricGridGenerator;
use Yzalis\Identicon\IdenticonBuilder;
use Yzalis\Identicon\Value\GridSize;

/**
 * Tests to verify v3 produces identical output to v2 when using compatibility mode.
 */
final class V2CompatibilityTest extends TestCase
{
    #[DataProvider('v2HashProvider')]
    public function testMd5HashMatchesV2(string $input, string $expectedHash): void
    {
        $algorithm = new Md5HashAlgorithm();
        $hash = $algorithm->hash($input);

        self::assertSame($expectedHash, $hash->hex);
    }

    public static function v2HashProvider(): iterable
    {
        yield 'foo' => ['foo', 'acbd18db4cc2f85cedef654fccc4a4d8'];
        yield 'bar' => ['bar', '37b51d194a7513e45b56f6524f2d51f2'];
        yield 'Benjamin' => ['Benjamin', '861a744bccc0da5432f097d5838e4b83'];
        yield 'yzalis' => ['yzalis', '3994931b4c7d62af19e32f38ee99cfe9'];
        yield '8.8.8.8' => ['8.8.8.8', '40ff44d9e619b17524bf3763204f9cbb'];
    }

    #[DataProvider('v2ColorProvider')]
    public function testLegacyColorExtractorMatchesV2(string $input, int $r, int $g, int $b): void
    {
        $hashAlgorithm = new Md5HashAlgorithm();
        $colorExtractor = new LegacyColorExtractor();

        $hash = $hashAlgorithm->hash($input);
        $color = $colorExtractor->extractColor($hash);

        self::assertSame($r, $color->red, "Red mismatch for input: {$input}");
        self::assertSame($g, $color->green, "Green mismatch for input: {$input}");
        self::assertSame($b, $color->blue, "Blue mismatch for input: {$input}");
    }

    public static function v2ColorProvider(): iterable
    {
        // Colors based on last 3 hex chars of MD5: hexdec(char) * 16
        // 'foo' hash ends with 4d8 → 4*16=64, 13*16=208, 8*16=128
        yield 'foo' => ['foo', 64, 208, 128];

        // 'bar' hash ends with 1f2 → 1*16=16, 15*16=240, 2*16=32
        yield 'bar' => ['bar', 16, 240, 32];

        // 'Benjamin' hash ends with b83 → 11*16=176, 8*16=128, 3*16=48
        yield 'Benjamin' => ['Benjamin', 176, 128, 48];
    }

    #[DataProvider('v2GridProvider')]
    public function testGridPatternMatchesV2(string $input, array $expectedPattern): void
    {
        $hashAlgorithm = new Md5HashAlgorithm();
        $gridGenerator = new SymmetricGridGenerator();

        $hash = $hashAlgorithm->hash($input);
        $grid = $gridGenerator->generate($hash, GridSize::default());

        $matrix = $grid->toMatrix();

        self::assertSame($expectedPattern, $matrix, "Grid pattern mismatch for input: {$input}");
    }

    public static function v2GridProvider(): iterable
    {
        // Pattern for 'bar' with hash 37b51d194a7513e45b56f6524f2d51f2
        yield 'bar' => [
            'bar',
            [
                [false, true, true, true, false],
                [true, true, false, true, true],
                [false, true, false, true, false],
                [true, false, true, false, true],
                [true, false, true, false, true],
            ],
        ];
    }

    public function testV2CompatibilityModeProducesSameOutput(): void
    {
        $identicon1 = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->useSvg()
            ->build();

        $identicon2 = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->useSvg()
            ->build();

        $output1 = $identicon1->getImageData('test@example.com');
        $output2 = $identicon2->getImageData('test@example.com');

        self::assertSame($output1, $output2);
    }

    public function testV2CompatibilityUsesMd5(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame('md5', $config->hashAlgorithm->getName());
    }

    public function testV2CompatibilityUses5x5Grid(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame(5, $config->gridSize->columns);
        self::assertSame(5, $config->gridSize->rows);
    }

    public function testV2CompatibilityUsesLegacyColorExtractor(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->build();

        $config = $identicon->getConfig();

        self::assertInstanceOf(LegacyColorExtractor::class, $config->colorExtractor);
    }

    public function testDifferentInputsProduceDifferentOutputs(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->useSvg()
            ->build();

        $output1 = $identicon->getImageData('user1@example.com');
        $output2 = $identicon->getImageData('user2@example.com');

        self::assertNotSame($output1, $output2);
    }

    public function testSymmetricGrid(): void
    {
        $hashAlgorithm = new Md5HashAlgorithm();
        $gridGenerator = new SymmetricGridGenerator();

        $hash = $hashAlgorithm->hash('symmetry-test');
        $grid = $gridGenerator->generate($hash, GridSize::default());
        $matrix = $grid->toMatrix();

        // Verify horizontal symmetry
        for ($row = 0; $row < 5; $row++) {
            self::assertSame($matrix[$row][0], $matrix[$row][4], "Row {$row}: col 0 should equal col 4");
            self::assertSame($matrix[$row][1], $matrix[$row][3], "Row {$row}: col 1 should equal col 3");
        }
    }
}
