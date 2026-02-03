<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Renderer;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Exception\ExtensionNotLoadedException;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\Color;

final class ImageMagickRenderer implements RendererInterface
{
    public function render(Grid $grid, Color $foregroundColor, IdenticonConfig $config): string
    {
        if (!$this->isAvailable()) {
            throw ExtensionNotLoadedException::forExtension('imagick');
        }

        $size = $config->size;
        $margin = $config->margin;

        $imageWidth = $size->width;
        $imageHeight = $size->height;

        $drawableWidth = $imageWidth - $margin->getHorizontal();
        $drawableHeight = $imageHeight - $margin->getVertical();

        $cellWidth = $drawableWidth / $grid->gridSize->columns;
        $cellHeight = $drawableHeight / $grid->gridSize->rows;

        $image = new Imagick();

        $bgColor = $config->backgroundColor !== null
            ? new ImagickPixel($config->backgroundColor->toHex())
            : new ImagickPixel('transparent');

        $image->newImage($imageWidth, $imageHeight, $bgColor);
        $image->setImageFormat('png');

        $draw = new ImagickDraw();
        $draw->setFillColor(new ImagickPixel($foregroundColor->toHex()));

        foreach ($grid->getFilledCells() as $cell) {
            $x1 = $margin->left + $cell->x * $cellWidth;
            $y1 = $margin->top + $cell->y * $cellHeight;
            $x2 = $margin->left + ($cell->x + 1) * $cellWidth - 1;
            $y2 = $margin->top + ($cell->y + 1) * $cellHeight - 1;

            $draw->rectangle($x1, $y1, $x2, $y2);
        }

        $image->drawImage($draw);

        $data = $image->getImageBlob();

        $image->clear();
        $image->destroy();

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
        return \extension_loaded('imagick');
    }
}
