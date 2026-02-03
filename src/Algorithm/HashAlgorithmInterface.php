<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Hash;

interface HashAlgorithmInterface
{
    public function hash(string $input): Hash;

    public function getName(): string;
}
