<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Exception;

final class ExtensionNotLoadedException extends IdenticonException
{
    public static function forExtension(string $extension): self
    {
        return new self(\sprintf(
            'The PHP extension "%s" is not loaded. Please install or enable it.',
            $extension,
        ));
    }
}
