<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Renderer;

use RuntimeException;
use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Exception\ExtensionNotLoadedException;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\Color;

final class GdRenderer implements RendererInterface
{
    public function render(Grid $grid, Color $foregroundColor, IdenticonConfig $config): string
    {
        if (!$this->isAvailable()) {
            throw ExtensionNotLoadedException::forExtension('gd');
        }

        $size = $config->size;
        $margin = $config->margin;

        $imageWidth = $size->width;
        $imageHeight = $size->height;

        $drawableWidth = $imageWidth - $margin->getHorizontal();
        $drawableHeight = $imageHeight - $margin->getVertical();

        $cellWidth = $drawableWidth / $grid->gridSize->columns;
        $cellHeight = $drawableHeight / $grid->gridSize->rows;

        $image = imagecreatetruecolor($imageWidth, $imageHeight);

        if ($image === false) {
            throw new RuntimeException('Failed to create image');
        }

        imagesavealpha($image, true);

        if ($config->backgroundColor !== null) {
            $bgColor = imagecolorallocate(
                $image,
                $config->backgroundColor->red,
                $config->backgroundColor->green,
                $config->backgroundColor->blue,
            );

            if ($bgColor !== false) {
                imagefill($image, 0, 0, $bgColor);
            }
        } else {
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

            if ($transparent !== false) {
                imagefill($image, 0, 0, $transparent);
            }
        }

        $fgColor = imagecolorallocate(
            $image,
            $foregroundColor->red,
            $foregroundColor->green,
            $foregroundColor->blue,
        );

        if ($fgColor === false) {
            throw new RuntimeException('Failed to allocate foreground color');
        }

        foreach ($grid->getFilledCells() as $cell) {
            $x1 = (int) round($margin->left + $cell->x * $cellWidth);
            $y1 = (int) round($margin->top + $cell->y * $cellHeight);
            $x2 = (int) round($margin->left + ($cell->x + 1) * $cellWidth) - 1;
            $y2 = (int) round($margin->top + ($cell->y + 1) * $cellHeight) - 1;

            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $fgColor);
        }

        ob_start();
        imagepng($image);
        $data = ob_get_clean();

        if ($data === false) {
            throw new RuntimeException('Failed to generate PNG data');
        }

        return $data;
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getFileExtension(): string
    {
        return 'png';
    }

    public function isAvailable(): bool
    {
        return \extension_loaded('gd');
    }
}
