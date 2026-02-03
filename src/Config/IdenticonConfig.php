<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Config;

use Yzalis\Identicon\Algorithm\ColorExtractorInterface;
use Yzalis\Identicon\Algorithm\GridGeneratorInterface;
use Yzalis\Identicon\Algorithm\HashAlgorithmInterface;
use Yzalis\Identicon\Algorithm\HslColorExtractor;
use Yzalis\Identicon\Algorithm\Sha256HashAlgorithm;
use Yzalis\Identicon\Algorithm\SymmetricGridGenerator;
use Yzalis\Identicon\Renderer\GdRenderer;
use Yzalis\Identicon\Renderer\RendererInterface;
use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\GridSize;
use Yzalis\Identicon\Value\Margin;
use Yzalis\Identicon\Value\Size;

final readonly class IdenticonConfig
{
    public function __construct(
        public Size $size,
        public GridSize $gridSize,
        public Margin $margin,
        public ?Color $foregroundColor,
        public ?Color $backgroundColor,
        public HashAlgorithmInterface $hashAlgorithm,
        public ColorExtractorInterface $colorExtractor,
        public GridGeneratorInterface $gridGenerator,
        public RendererInterface $renderer,
        public bool $skipHashing = false,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            size: Size::default(),
            gridSize: GridSize::default(),
            margin: Margin::none(),
            foregroundColor: null,
            backgroundColor: null,
            hashAlgorithm: new Sha256HashAlgorithm(),
            colorExtractor: new HslColorExtractor(),
            gridGenerator: new SymmetricGridGenerator(),
            renderer: new GdRenderer(),
            skipHashing: false,
        );
    }
}
