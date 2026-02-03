<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Value;

use InvalidArgumentException;

final readonly class Hash
{
    private string $binary;

    public function __construct(
        public string $hex,
    ) {
        $this->validate();
        $this->binary = (string) hex2bin($this->hex);
    }

    public static function fromBinary(string $binary): self
    {
        return new self(bin2hex($binary));
    }

    public function getBytes(): string
    {
        return $this->binary;
    }

    public function getByte(int $index): int
    {
        if ($index < 0 || $index >= \strlen($this->binary)) {
            throw new InvalidArgumentException(\sprintf(
                'Byte index %d is out of range. Hash has %d bytes.',
                $index,
                \strlen($this->binary),
            ));
        }

        return \ord($this->binary[$index]);
    }

    public function getLength(): int
    {
        return \strlen($this->binary);
    }

    public function getHexLength(): int
    {
        return \strlen($this->hex);
    }

    /**
     * @return array<int>
     */
    public function toByteArray(): array
    {
        return array_map('ord', str_split($this->binary));
    }

    private function validate(): void
    {
        if (!preg_match('/^[a-fA-F0-9]+$/', $this->hex)) {
            throw new InvalidArgumentException(
                'Hash must be a valid hexadecimal string.',
            );
        }

        if (\strlen($this->hex) % 2 !== 0) {
            throw new InvalidArgumentException(
                'Hash hex string must have an even number of characters.',
            );
        }

        if (\strlen($this->hex) < 8) {
            throw new InvalidArgumentException(
                'Hash must be at least 8 hex characters (4 bytes).',
            );
        }
    }
}
