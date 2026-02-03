<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Exception;

final class InvalidGridSizeException extends IdenticonException
{
    public static function columnsOutOfRange(int $columns, int $min, int $max): self
    {
        return new self(\sprintf(
            'Grid columns %d is out of range. Expected %d-%d.',
            $columns,
            $min,
            $max,
        ));
    }

    public static function rowsOutOfRange(int $rows, int $min, int $max): self
    {
        return new self(\sprintf(
            'Grid rows %d is out of range. Expected %d-%d.',
            $rows,
            $min,
            $max,
        ));
    }
}
