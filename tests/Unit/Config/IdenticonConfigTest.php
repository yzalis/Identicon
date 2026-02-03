<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yzalis\Identicon\Algorithm\HslColorExtractor;
use Yzalis\Identicon\Algorithm\Sha256HashAlgorithm;
use Yzalis\Identicon\Algorithm\SymmetricGridGenerator;
use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Renderer\GdRenderer;
use Yzalis\Identicon\Value\GridSize;
use Yzalis\Identicon\Value\Margin;
use Yzalis\Identicon\Value\Size;

final class IdenticonConfigTest extends TestCase
{
    public function testDefaults(): void
    {
        $config = IdenticonConfig::defaults();

        self::assertSame(64, $config->size->width);
        self::assertSame(64, $config->size->height);
        self::assertSame(5, $config->gridSize->columns);
        self::assertSame(5, $config->gridSize->rows);
        self::assertTrue($config->margin->isEmpty());
        self::assertNull($config->foregroundColor);
        self::assertNull($config->backgroundColor);
        self::assertInstanceOf(Sha256HashAlgorithm::class, $config->hashAlgorithm);
        self::assertInstanceOf(HslColorExtractor::class, $config->colorExtractor);
        self::assertInstanceOf(SymmetricGridGenerator::class, $config->gridGenerator);
        self::assertInstanceOf(GdRenderer::class, $config->renderer);
    }

    public function testCustomConfig(): void
    {
        $config = new IdenticonConfig(
            size: Size::square(128),
            gridSize: GridSize::square(7),
            margin: Margin::all(10),
            foregroundColor: null,
            backgroundColor: null,
            hashAlgorithm: new Sha256HashAlgorithm(),
            colorExtractor: new HslColorExtractor(),
            gridGenerator: new SymmetricGridGenerator(),
            renderer: new GdRenderer(),
        );

        self::assertSame(128, $config->size->width);
        self::assertSame(7, $config->gridSize->columns);
        self::assertSame(10, $config->margin->top);
    }

    public function testConfigIsReadonly(): void
    {
        $config = IdenticonConfig::defaults();

        $reflection = new ReflectionClass($config);
        self::assertTrue($reflection->isReadOnly());
    }
}
