<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Renderer;

use Yzalis\Identicon\Config\IdenticonConfig;
use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\Color;

interface RendererInterface
{
    public function render(Grid $grid, Color $foregroundColor, IdenticonConfig $config): string;

    public function getMimeType(): string;

    public function getFileExtension(): string;

    public function isAvailable(): bool;
}
