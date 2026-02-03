<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Grid;

final readonly class Cell
{
    public function __construct(
        public int $x,
        public int $y,
        public bool $filled,
    ) {
    }

    public function isEmpty(): bool
    {
        return !$this->filled;
    }
}
