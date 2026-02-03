<?php

declare(strict_types=1);

namespace Yzalis\Identicon\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Yzalis\Identicon\IdenticonBuilder;

/**
 * Tests for the skipHashing feature (Issue #79).
 *
 * Use case: GDPR compliance and pre-computed hashes.
 *
 * When storing user emails as hashes for privacy (GDPR), you may want to
 * generate identicons directly from those hashes without re-hashing them.
 */
final class SkipHashingTest extends TestCase
{
    public function testSkipHashingProducesSameResultAsHashedOriginal(): void
    {
        // The original email
        $email = 'john@example.com';

        // Pre-compute the MD5 hash (as stored in database for GDPR)
        $md5Hash = md5($email);

        // Generate identicon from original email (normal mode)
        $identiconNormal = (new IdenticonBuilder())
            ->useMd5()
            ->useSvg()
            ->build();

        // Generate identicon from pre-computed hash (skip hashing mode)
        $identiconSkip = (new IdenticonBuilder())
            ->skipHashing()
            ->useSvg()
            ->build();

        $fromEmail = $identiconNormal->getImageData($email);
        $fromHash = $identiconSkip->getImageData($md5Hash);

        self::assertSame($fromEmail, $fromHash);
    }

    public function testSkipHashingWithSha256(): void
    {
        $input = 'test@example.com';
        $sha256Hash = hash('sha256', $input);

        // Normal SHA-256 identicon
        $identiconNormal = (new IdenticonBuilder())
            ->useSha256()
            ->useSvg()
            ->build();

        // Skip hashing identicon
        $identiconSkip = (new IdenticonBuilder())
            ->skipHashing()
            ->useSvg()
            ->build();

        $fromInput = $identiconNormal->getImageData($input);
        $fromHash = $identiconSkip->getImageData($sha256Hash);

        self::assertSame($fromInput, $fromHash);
    }

    public function testSkipHashingColorMatchesNormalMode(): void
    {
        $email = 'user@domain.com';
        $md5Hash = md5($email);

        $identiconNormal = (new IdenticonBuilder())
            ->useMd5()
            ->build();

        $identiconSkip = (new IdenticonBuilder())
            ->skipHashing()
            ->build();

        $colorFromEmail = $identiconNormal->getColor($email);
        $colorFromHash = $identiconSkip->getColor($md5Hash);

        self::assertSame($colorFromEmail->toHex(), $colorFromHash->toHex());
    }

    public function testSkipHashingWithGdRenderer(): void
    {
        if (!\extension_loaded('gd')) {
            self::markTestSkipped('GD extension not available');
        }

        $email = 'gd-test@example.com';
        $md5Hash = md5($email);

        $identiconNormal = (new IdenticonBuilder())
            ->useMd5()
            ->useGd()
            ->build();

        $identiconSkip = (new IdenticonBuilder())
            ->skipHashing()
            ->useGd()
            ->build();

        $fromEmail = $identiconNormal->getImageData($email);
        $fromHash = $identiconSkip->getImageData($md5Hash);

        self::assertSame($fromEmail, $fromHash);
    }

    public function testSkipHashingConfigIsSet(): void
    {
        $identicon = (new IdenticonBuilder())
            ->skipHashing()
            ->build();

        self::assertTrue($identicon->getConfig()->skipHashing);
    }

    public function testSkipHashingDefaultIsFalse(): void
    {
        $identicon = (new IdenticonBuilder())->build();

        self::assertFalse($identicon->getConfig()->skipHashing);
    }

    public function testGdprUseCase(): void
    {
        // Scenario: Database stores hashed emails for GDPR compliance
        // We want to display identicons without knowing the original email

        $storedHash = 'acbd18db4cc2f85cedef654fccc4a4d8'; // MD5 of "foo"

        $identicon = (new IdenticonBuilder())
            ->skipHashing()
            ->size(64)
            ->useSvg()
            ->build();

        $svg = $identicon->getImageData($storedHash);

        // Verify it generates a valid SVG
        self::assertStringStartsWith('<svg', $svg);
        self::assertStringContainsString('width="64"', $svg);
    }

    public function testDifferentHashesProduceDifferentIdenticons(): void
    {
        $identicon = (new IdenticonBuilder())
            ->skipHashing()
            ->useSvg()
            ->build();

        $hash1 = md5('user1@example.com');
        $hash2 = md5('user2@example.com');

        $svg1 = $identicon->getImageData($hash1);
        $svg2 = $identicon->getImageData($hash2);

        self::assertNotSame($svg1, $svg2);
    }
}
