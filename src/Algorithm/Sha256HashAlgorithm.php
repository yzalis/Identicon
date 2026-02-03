<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Algorithm;

use Yzalis\Identicon\Value\Hash;

final class Sha256HashAlgorithm implements HashAlgorithmInterface
{
    public function hash(string $input): Hash
    {
        return new Hash(hash('sha256', $input));
    }

    public function getName(): string
    {
        return 'sha256';
    }
}
