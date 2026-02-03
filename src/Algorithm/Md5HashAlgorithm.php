<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Hash;

final class Md5HashAlgorithm implements HashAlgorithmInterface
{
    public function hash(string $input): Hash
    {
        return new Hash(md5($input));
    }

    public function getName(): string
    {
        return 'md5';
    }
}
