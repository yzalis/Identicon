# Identicon

[![CI](https://img.shields.io/github/actions/workflow/status/yzalis/Identicon/ci.yml?style=flat-square&logo=github&label=CI)](https://github.com/yzalis/Identicon/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/yzalis/identicon?style=flat-square&logo=packagist&logoColor=white)](https://packagist.org/packages/yzalis/identicon)
[![PHP Version](https://img.shields.io/packagist/php-v/yzalis/identicon?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Downloads](https://img.shields.io/packagist/dt/yzalis/identicon?style=flat-square&logo=packagist&logoColor=white)](https://packagist.org/packages/yzalis/identicon)
[![License](https://img.shields.io/github/license/yzalis/Identicon?style=flat-square)](LICENSE)

Generate unique identicon avatars from any string.

## Installation

```bash
composer require yzalis/identicon
```

## Requirements

- PHP 8.1+
- GD extension (for PNG output) or Imagick extension (optional)

## Quick Start

```php
use Yzalis\Identicon\Identicon;

// Generate a data URI for an img tag
$dataUri = Identicon::generate('john@example.com');
echo '<img src="' . $dataUri . '" alt="Avatar" />';

// With custom size
$dataUri = Identicon::generate('john@example.com', 128);
```

## Builder Pattern

For more control, use the fluent builder:

```php
use Yzalis\Identicon\IdenticonBuilder;

$identicon = (new IdenticonBuilder())
    ->size(256)
    ->gridSize(7)
    ->margin(16)
    ->color('#3498db')
    ->backgroundColor('#ffffff')
    ->useSvg()
    ->build();

$svg = $identicon->getImageData('john@example.com');
$dataUri = $identicon->getImageDataUri('john@example.com');
```

## Configuration Options

### Size

```php
// Square image
$builder->size(128);  // 128x128 pixels

// Rectangle (if needed)
$builder->dimensions(200, 100);
```

### Grid Size

The grid determines the pattern complexity (default: 5x5).

```php
$builder->gridSize(7);  // 7x7 grid

// Auto-calculate based on image size
$builder->size(1024)->autoGridSize();  // ~17x17 grid
```

### Margins

```php
// Uniform margin
$builder->margin(16);  // 16px all sides

// Percentage margin
$builder->marginPercent(10);  // 10% of image size

// Per-side margin
$builder->margin([10, 20, 10, 20]);  // top, right, bottom, left
```

### Colors

```php
// Hex colors
$builder->color('#3498db');
$builder->color('fff');  // Short format

// RGB array
$builder->color([52, 152, 219]);

// Background
$builder->backgroundColor('#ffffff');
$builder->backgroundColor(null);  // Transparent (default)
```

### Renderers

```php
$builder->useSvg();         // SVG (vector, no extension needed)
$builder->useGd();          // PNG via GD (default)
$builder->useImageMagick(); // PNG via ImageMagick
```

### Hash Algorithms

```php
$builder->useSha256();  // Default, better distribution
$builder->useMd5();     // For v2 compatibility
```

### Pre-computed Hashes (GDPR Compliance)

If you already have pre-computed hashes (e.g., for GDPR compliance where emails
are stored as hashes), you can generate identicons directly from those hashes
without re-hashing:

```php
// Database stores hashed emails for privacy (GDPR)
$storedHash = 'acbd18db4cc2f85cedef654fccc4a4d8';  // MD5 of user email

$identicon = (new IdenticonBuilder())
    ->skipHashing()  // Use input directly as hash
    ->useSvg()
    ->build();

$avatar = $identicon->getImageDataUri($storedHash);
```

This ensures consistency: generating an identicon from `"john@example.com"` with
MD5 hashing produces the same result as generating from its pre-computed MD5 hash
with `skipHashing()` enabled.

```php
// These produce identical identicons:
$email = 'john@example.com';
$hash = md5($email);

// Method 1: Hash the email
$id1 = (new IdenticonBuilder())->useMd5()->build();
$avatar1 = $id1->getImageData($email);

// Method 2: Use pre-computed hash directly
$id2 = (new IdenticonBuilder())->skipHashing()->build();
$avatar2 = $id2->getImageData($hash);

// $avatar1 === $avatar2
```

## Output Methods

```php
$identicon = (new IdenticonBuilder())->build();

// Get binary image data
$data = $identicon->getImageData('email@example.com');

// Get base64 data URI (for img src)
$dataUri = $identicon->getImageDataUri('email@example.com');

// Get raw base64 string
$base64 = $identicon->getImageBase64('email@example.com');

// Output directly to browser
$identicon->displayImage('email@example.com');

// Save to file
$identicon->saveToFile('email@example.com', '/path/to/avatar.png');

// Get the generated color
$color = $identicon->getColor('email@example.com');
echo $color->toHex();  // #3498db
```

## v2 Compatibility

If you're upgrading from v2 and need to keep the same identicons:

```php
$identicon = (new IdenticonBuilder())
    ->useV2Compatibility()
    ->build();
```

This activates:
- MD5 hashing (instead of SHA-256)
- Legacy color extraction algorithm
- 5x5 grid

See [UPGRADE-3.0.md](UPGRADE-3.0.md) for full migration guide.

## Advanced Usage

### Custom Algorithms

```php
use Yzalis\Identicon\Algorithm\HashAlgorithmInterface;
use Yzalis\Identicon\Value\Hash;

class MyHashAlgorithm implements HashAlgorithmInterface
{
    public function hash(string $input): Hash
    {
        return new Hash(hash('sha512', $input));
    }

    public function getName(): string
    {
        return 'sha512';
    }
}

$identicon = (new IdenticonBuilder())
    ->hashAlgorithm(new MyHashAlgorithm())
    ->build();
```

### Custom Renderers

```php
use Yzalis\Identicon\Renderer\RendererInterface;

class WebPRenderer implements RendererInterface
{
    // Implement render(), getMimeType(), getFileExtension(), isAvailable()
}

$identicon = (new IdenticonBuilder())
    ->renderer(new WebPRenderer())
    ->build();
```

## Testing

```bash
composer install
composer test
```

## Static Analysis

```bash
composer analyze
```

## Code Style

```bash
composer cs       # Check
composer cs:fix   # Fix
```

## License

MIT License. See [LICENSE](LICENSE) for details.

## Credits

* Benjamin Laugueux <benjamin@laugueux.org>
* [All contributors](https://github.com/yzalis/Identicon/graphs/contributors)

Inspired by GitHub's [blog post](https://github.com/blog/1586-identicons) about identicons.
