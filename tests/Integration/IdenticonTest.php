<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Identicon;
use Yzalis\Identicon\IdenticonBuilder;

final class IdenticonTest extends TestCase
{
    public function testStaticGenerate(): void
    {
        $dataUri = Identicon::generate('test@example.com');

        self::assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    public function testStaticGenerateWithSize(): void
    {
        $dataUri = Identicon::generate('test@example.com', 128);

        self::assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    public function testBuilderWithSvg(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test@example.com');

        self::assertStringStartsWith('<svg', $data);
        self::assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $data);
    }

    public function testBuilderWithCustomColor(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->color('#ff0000')
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        self::assertStringContainsString('#ff0000', $data);
    }

    public function testBuilderWithBackgroundColor(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(64)
            ->backgroundColor('#ffffff')
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        self::assertStringContainsString('#ffffff', $data);
    }

    public function testBuilderWithMargin(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(100)
            ->margin(10)
            ->useSvg()
            ->build();

        $data = $identicon->getImageData('test');

        self::assertStringContainsString('width="100"', $data);
        self::assertStringContainsString('height="100"', $data);
    }

    public function testBuilderWithGridSize(): void
    {
        $identicon = (new IdenticonBuilder())
            ->size(128)
            ->gridSize(7)
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame(7, $config->gridSize->columns);
        self::assertSame(7, $config->gridSize->rows);
    }

    public function testV2Compatibility(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useV2Compatibility()
            ->useSvg()
            ->build();

        $config = $identicon->getConfig();

        self::assertSame('md5', $config->hashAlgorithm->getName());
        self::assertSame(5, $config->gridSize->columns);
    }

    public function testGetColor(): void
    {
        $identicon = (new IdenticonBuilder())->build();
        $color = $identicon->getColor('test@example.com');

        self::assertGreaterThanOrEqual(0, $color->red);
        self::assertLessThanOrEqual(255, $color->red);
    }

    public function testDeterministicOutput(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useSvg()
            ->build();

        $data1 = $identicon->getImageData('consistent-input');
        $data2 = $identicon->getImageData('consistent-input');

        self::assertSame($data1, $data2);
    }

    public function testDifferentInputsDifferentOutput(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useSvg()
            ->build();

        $data1 = $identicon->getImageData('input1');
        $data2 = $identicon->getImageData('input2');

        self::assertNotSame($data1, $data2);
    }

    public function testDataUri(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useSvg()
            ->build();

        $dataUri = $identicon->getImageDataUri('test');

        self::assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);
    }

    public function testBase64(): void
    {
        $identicon = (new IdenticonBuilder())
            ->useSvg()
            ->build();

        $base64 = $identicon->getImageBase64('test');

        self::assertNotEmpty($base64);
        self::assertSame(base64_decode($base64, true) !== false, true);
    }
}
