<?php

declare(strict_types=1);

namespace Yzalis\Identicon;

use Yzalis\Identicon\Algorithm\ColorExtractorInterface;
use Yzalis\Identicon\Algorithm\GridGeneratorInterface;
use Yzalis\Identicon\Algorithm\HashAlgorithmInterface;
use Yzalis\Identicon\Algorithm\HslColorExtractor;
use Yzalis\Identicon\Algorithm\LegacyColorExtractor;
use Yzalis\Identicon\Algorithm\Md5HashAlgorithm;
use Yzalis\Identicon\Algorithm\Sha256HashAlgorithm;
use Yzalis\Identicon\Algorithm\SymmetricGridGenerator;
use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Renderer\GdRenderer;
use Yzalis\Identicon\Renderer\ImageMagickRenderer;
use Yzalis\Identicon\Renderer\RendererInterface;
use Yzalis\Identicon\Renderer\SvgRenderer;
use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\GridSize;
use Yzalis\Identicon\Value\Margin;
use Yzalis\Identicon\Value\Size;

final class IdenticonBuilder
{
    private Size $size;
    private GridSize $gridSize;
    private Margin $margin;
    private ?Color $foregroundColor = null;
    private ?Color $backgroundColor = null;
    private HashAlgorithmInterface $hashAlgorithm;
    private ColorExtractorInterface $colorExtractor;
    private GridGeneratorInterface $gridGenerator;
    private RendererInterface $renderer;
    private bool $skipHashing = false;

    public function __construct()
    {
        $this->size = Size::default();
        $this->gridSize = GridSize::default();
        $this->margin = Margin::none();
        $this->hashAlgorithm = new Sha256HashAlgorithm();
        $this->colorExtractor = new HslColorExtractor();
        $this->gridGenerator = new SymmetricGridGenerator();
        $this->renderer = new GdRenderer();
    }

    public function size(int $pixels): self
    {
        $clone = clone $this;
        $clone->size = Size::square($pixels);

        return $clone;
    }

    public function dimensions(int $width, int $height): self
    {
        $clone = clone $this;
        $clone->size = new Size($width, $height);

        return $clone;
    }

    public function gridSize(int $columns, ?int $rows = null): self
    {
        $clone = clone $this;
        $clone->gridSize = $rows === null
            ? GridSize::square($columns)
            : new GridSize($columns, $rows);

        return $clone;
    }

    public function autoGridSize(): self
    {
        $clone = clone $this;
        $clone->gridSize = GridSize::forImageSize($this->size->getMinDimension());

        return $clone;
    }

    /**
     * @param int|array<int|string, int> $margin
     */
    public function margin(int|array $margin): self
    {
        $clone = clone $this;
        $clone->margin = Margin::from($margin);

        return $clone;
    }

    public function marginPercent(float $percent): self
    {
        $clone = clone $this;
        $clone->margin = Margin::percent($this->size->getMinDimension(), $percent);

        return $clone;
    }

    /**
     * @param string|array<int|string, int>|null $color
     */
    public function color(string|array|null $color): self
    {
        $clone = clone $this;
        $clone->foregroundColor = $color !== null ? Color::from($color) : null;

        return $clone;
    }

    /**
     * @param string|array<int|string, int>|null $color
     */
    public function backgroundColor(string|array|null $color): self
    {
        $clone = clone $this;
        $clone->backgroundColor = $color !== null ? Color::from($color) : null;

        return $clone;
    }

    public function renderer(RendererInterface $renderer): self
    {
        $clone = clone $this;
        $clone->renderer = $renderer;

        return $clone;
    }

    public function useSvg(): self
    {
        return $this->renderer(new SvgRenderer());
    }

    public function useGd(): self
    {
        return $this->renderer(new GdRenderer());
    }

    public function useImageMagick(): self
    {
        return $this->renderer(new ImageMagickRenderer());
    }

    public function hashAlgorithm(HashAlgorithmInterface $algorithm): self
    {
        $clone = clone $this;
        $clone->hashAlgorithm = $algorithm;

        return $clone;
    }

    public function useSha256(): self
    {
        return $this->hashAlgorithm(new Sha256HashAlgorithm());
    }

    public function useMd5(): self
    {
        return $this->hashAlgorithm(new Md5HashAlgorithm());
    }

    public function colorExtractor(ColorExtractorInterface $extractor): self
    {
        $clone = clone $this;
        $clone->colorExtractor = $extractor;

        return $clone;
    }

    public function gridGenerator(GridGeneratorInterface $generator): self
    {
        $clone = clone $this;
        $clone->gridGenerator = $generator;

        return $clone;
    }

    /**
     * Enable v2 compatibility mode.
     * Uses MD5 hash, legacy color extractor, and 5x5 grid.
     */
    public function useV2Compatibility(): self
    {
        $clone = clone $this;
        $clone->hashAlgorithm = new Md5HashAlgorithm();
        $clone->colorExtractor = new LegacyColorExtractor();
        $clone->gridSize = GridSize::square(5);

        return $clone;
    }

    /**
     * Skip hashing and use the input string directly as a hash.
     *
     * Useful when you already have pre-computed hashes (e.g., MD5, SHA-256)
     * and want to generate identicons from them without re-hashing.
     *
     * Common use cases:
     * - GDPR compliance: when emails are stored as hashes for privacy,
     *   you can generate identicons directly from those hashes
     * - Performance: avoid redundant hashing when hash is already available
     * - Consistency: ensure "user@example.com" and its MD5 hash produce
     *   the same identicon
     *
     * Note: The input must be a valid hexadecimal string (at least 8 chars).
     */
    public function skipHashing(): self
    {
        $clone = clone $this;
        $clone->skipHashing = true;

        return $clone;
    }

    public function build(): Identicon
    {
        $config = new IdenticonConfig(
            size: $this->size,
            gridSize: $this->gridSize,
            margin: $this->margin,
            foregroundColor: $this->foregroundColor,
            backgroundColor: $this->backgroundColor,
            hashAlgorithm: $this->hashAlgorithm,
            colorExtractor: $this->colorExtractor,
            gridGenerator: $this->gridGenerator,
            renderer: $this->renderer,
            skipHashing: $this->skipHashing,
        );

        return new Identicon($config);
    }
}
