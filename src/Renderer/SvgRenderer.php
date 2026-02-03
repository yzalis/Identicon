<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Renderer;

use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\Color;

final class SvgRenderer implements RendererInterface
{
    public function render(Grid $grid, Color $foregroundColor, IdenticonConfig $config): string
    {
        $size = $config->size;
        $margin = $config->margin;
        $gridSize = $grid->gridSize;

        $drawableWidth = $size->width - $margin->getHorizontal();
        $drawableHeight = $size->height - $margin->getVertical();

        $cellWidth = $drawableWidth / $gridSize->columns;
        $cellHeight = $drawableHeight / $gridSize->rows;

        $backgroundColor = $config->backgroundColor;
        $bgRect = '';

        if ($backgroundColor !== null) {
            $bgRect = \sprintf(
                '<rect width="%d" height="%d" fill="%s"/>',
                $size->width,
                $size->height,
                $backgroundColor->toHexLower(),
            );
        }

        $pathData = $this->buildPathData($grid, $margin, $cellWidth, $cellHeight);

        $path = '';
        if ($pathData !== '') {
            $path = \sprintf(
                '<path fill="%s" d="%s"/>',
                $foregroundColor->toHexLower(),
                $pathData,
            );
        }

        return \sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">%s%s</svg>',
            $size->width,
            $size->height,
            $size->width,
            $size->height,
            $bgRect,
            $path,
        );
    }

    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }

    public function getFileExtension(): string
    {
        return 'svg';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    private function buildPathData(Grid $grid, \Yzalis\Identicon\Value\Margin $margin, float $cellWidth, float $cellHeight): string
    {
        $commands = [];

        foreach ($grid->getFilledCells() as $cell) {
            $x = round($margin->left + $cell->x * $cellWidth, 2);
            $y = round($margin->top + $cell->y * $cellHeight, 2);
            $w = round($cellWidth, 2);
            $h = round($cellHeight, 2);

            $commands[] = \sprintf(
                'M%s %sh%sv%sh-%sZ',
                $this->formatNumber($x),
                $this->formatNumber($y),
                $this->formatNumber($w),
                $this->formatNumber($h),
                $this->formatNumber($w),
            );
        }

        return implode('', $commands);
    }

    private function formatNumber(float $value): string
    {
        if ($value == (int) $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
