<?php

declare(strict_types=1);

namespace Yzalis\Identicon;

use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Renderer\RendererInterface;
use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\Hash;

final class Identicon
{
    private IdenticonConfig $config;

    public function __construct(?IdenticonConfig $config = null)
    {
        $this->config = $config ?? IdenticonConfig::defaults();
    }

    /**
     * Quick generation with sensible defaults.
     *
     * @param string|array<int|string, int>|null $color
     * @param string|array<int|string, int>|null $backgroundColor
     */
    public static function generate(
        string $string,
        int $size = 64,
        string|array|null $color = null,
        string|array|null $backgroundColor = null,
    ): string {
        $builder = (new IdenticonBuilder())->size($size);

        if ($color !== null) {
            $builder = $builder->color($color);
        }

        if ($backgroundColor !== null) {
            $builder = $builder->backgroundColor($backgroundColor);
        }

        return $builder->build()->getImageDataUri($string);
    }

    public function getImageData(string $string): string
    {
        $result = $this->process($string);

        return $result['data'];
    }

    public function getImageDataUri(string $string): string
    {
        $result = $this->process($string);

        return \sprintf(
            'data:%s;base64,%s',
            $result['mimeType'],
            base64_encode($result['data']),
        );
    }

    public function getImageBase64(string $string): string
    {
        $result = $this->process($string);

        return base64_encode($result['data']);
    }

    public function displayImage(string $string): void
    {
        $result = $this->process($string);

        header('Content-Type: ' . $result['mimeType']);
        echo $result['data'];
    }

    public function saveToFile(string $string, string $path): bool
    {
        $result = $this->process($string);

        return file_put_contents($path, $result['data']) !== false;
    }

    public function getColor(string $string): Color
    {
        if ($this->config->foregroundColor !== null) {
            return $this->config->foregroundColor;
        }

        return $this->config->colorExtractor->extractColor($this->computeHash($string));
    }

    public function getConfig(): IdenticonConfig
    {
        return $this->config;
    }

    public function getRenderer(): RendererInterface
    {
        return $this->config->renderer;
    }

    /**
     * @return array{data: string, mimeType: string}
     */
    private function process(string $string): array
    {
        $hash = $this->computeHash($string);

        $foregroundColor = $this->config->foregroundColor
            ?? $this->config->colorExtractor->extractColor($hash);

        $grid = $this->config->gridGenerator->generate($hash, $this->config->gridSize);

        $data = $this->config->renderer->render($grid, $foregroundColor, $this->config);

        return [
            'data' => $data,
            'mimeType' => $this->config->renderer->getMimeType(),
        ];
    }

    private function computeHash(string $string): Hash
    {
        return $this->config->skipHashing
            ? new Hash($string)
            : $this->config->hashAlgorithm->hash($string);
    }
}
