<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Unit\Algorithm;

use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\Algorithm\Sha256HashAlgorithm;
use Yzalis\Identicon\Algorithm\SymmetricGridGenerator;
use Yzalis\Identicon\Value\GridSize;

final class SymmetricGridGeneratorTest extends TestCase
{
    private SymmetricGridGenerator $generator;
    private Sha256HashAlgorithm $hashAlgorithm;

    protected function setUp(): void
    {
        $this->generator = new SymmetricGridGenerator();
        $this->hashAlgorithm = new Sha256HashAlgorithm();
    }

    public function testGeneratesSymmetricGrid(): void
    {
        $hash = $this->hashAlgorithm->hash('test@example.com');
        $gridSize = GridSize::square(5);

        $grid = $this->generator->generate($hash, $gridSize);
        $matrix = $grid->toMatrix();

        for ($row = 0; $row < 5; $row++) {
            self::assertSame($matrix[$row][0], $matrix[$row][4]);
            self::assertSame($matrix[$row][1], $matrix[$row][3]);
        }
    }

    public function testDeterministicOutput(): void
    {
        $hash = $this->hashAlgorithm->hash('consistent-input');
        $gridSize = GridSize::default();

        $grid1 = $this->generator->generate($hash, $gridSize);
        $grid2 = $this->generator->generate($hash, $gridSize);

        self::assertEquals($grid1->toMatrix(), $grid2->toMatrix());
    }

    public function testDifferentInputsDifferentGrids(): void
    {
        $hash1 = $this->hashAlgorithm->hash('input1');
        $hash2 = $this->hashAlgorithm->hash('input2');
        $gridSize = GridSize::default();

        $grid1 = $this->generator->generate($hash1, $gridSize);
        $grid2 = $this->generator->generate($hash2, $gridSize);

        self::assertNotEquals($grid1->toMatrix(), $grid2->toMatrix());
    }

    public function testMinimumFillRatio(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $hash = $this->hashAlgorithm->hash("test-input-{$i}");
            $gridSize = GridSize::default();

            $grid = $this->generator->generate($hash, $gridSize);
            $ratio = $grid->getFillRatio();

            self::assertGreaterThanOrEqual(0.25, $ratio, "Input {$i} has fill ratio {$ratio}");
        }
    }

    public function testCorrectCellCount(): void
    {
        $hash = $this->hashAlgorithm->hash('test');
        $gridSize = new GridSize(7, 5);

        $grid = $this->generator->generate($hash, $gridSize);

        self::assertSame(35, $gridSize->getTotalCells());
        self::assertCount(35, $grid->getCells());
    }
}
