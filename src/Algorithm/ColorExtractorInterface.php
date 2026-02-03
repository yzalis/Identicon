<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Color;
use Yzalis\Identicon\Value\Hash;

interface ColorExtractorInterface
{
    public function extractColor(Hash $hash): Color;
}
