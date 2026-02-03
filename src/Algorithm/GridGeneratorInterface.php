<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Grid\Grid;
use Yzalis\Identicon\Value\GridSize;
use Yzalis\Identicon\Value\Hash;

interface GridGeneratorInterface
{
    public function generate(Hash $hash, GridSize $gridSize): Grid;
}
