# Upgrading from v2 to v3

This guide helps you migrate from Identicon v2 to v3.

## Breaking Changes

### PHP Version
- **v2**: PHP 5.5+
- **v3**: PHP 8.1+ (required for readonly classes, enums, etc.)

### Namespace Change
```php
// v2
use Identicon\Identicon;

// v3
use Yzalis\Identicon\Identicon;
```

### Hash Algorithm
- **v2**: MD5 (hardcoded)
- **v3**: SHA-256 (default), MD5 available via `useMd5()`

**This means identicons will look different for the same input!**

### API Changes

#### Constructor
```php
// v2
$identicon = new Identicon();
$identicon = new Identicon(new SvgGenerator());

// v3
$identicon = new Identicon();
$identicon = (new IdenticonBuilder())->useSvg()->build();
```

#### Generating Images
```php
// v2 - parameters on each call
$identicon->displayImage('email@example.com', 128, '#ff0000', '#ffffff');
$data = $identicon->getImageData('email@example.com', 128, '#ff0000', '#ffffff');
$uri = $identicon->getImageDataUri('email@example.com', 128);

// v3 - configure once, generate multiple
$identicon = (new IdenticonBuilder())
    ->size(128)
    ->color('#ff0000')
    ->backgroundColor('#ffffff')
    ->build();

$identicon->displayImage('email@example.com');
$data = $identicon->getImageData('email@example.com');
$uri = $identicon->getImageDataUri('email@example.com');

// v3 - quick one-liner
$uri = Identicon::generate('email@example.com', 128);
```

#### Removed Methods
- `setGenerator()` - Use `IdenticonBuilder->renderer()` instead
- `getImageResource()` - Use `getImageData()` instead

#### Changed Return Types
```php
// v2
$color = $identicon->getColor(); // Returns array ['r' => X, 'g' => Y, 'b' => Z]

// v3
$color = $identicon->getColor('email@example.com'); // Returns Color object
echo $color->red;
echo $color->toHex();
```

## Keeping v2 Visual Compatibility

If you need the **same identicons** as v2 (important for existing user avatars):

```php
use Yzalis\Identicon\IdenticonBuilder;

$identicon = (new IdenticonBuilder())
    ->useV2Compatibility()  // Activates MD5 + legacy color extraction + 5×5 grid
    ->build();

$uri = $identicon->getImageDataUri('email@example.com');
```

## New Features in v3

### Grid Size Configuration
```php
// Custom grid size (3-25)
$identicon = (new IdenticonBuilder())
    ->gridSize(7)  // 7×7 grid instead of default 5×5
    ->build();

// Auto-calculate based on image size
$identicon = (new IdenticonBuilder())
    ->size(1024)
    ->autoGridSize()  // Will use ~17×17 grid
    ->build();
```

### Margin Support
```php
// Fixed margin in pixels
$identicon = (new IdenticonBuilder())
    ->margin(16)
    ->build();

// Margin as percentage
$identicon = (new IdenticonBuilder())
    ->size(200)
    ->marginPercent(10)  // 20px margin
    ->build();

// Different margins per side
$identicon = (new IdenticonBuilder())
    ->margin(['top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20])
    ->build();
```

### SHA-256 Hash (Better Distribution)
```php
// v3 uses SHA-256 by default
$identicon = (new IdenticonBuilder())->build();

// Explicitly choose algorithm
$identicon = (new IdenticonBuilder())
    ->useSha256()  // or ->useMd5()
    ->build();
```

### Improved Color Extraction
v3 uses HSL color space for better color distribution:
- Guaranteed vibrant colors (45-75% saturation)
- Guaranteed visible colors (40-60% lightness)
- Better color variety across inputs

### Save to File
```php
$identicon->saveToFile('email@example.com', '/path/to/avatar.png');
```

## Migration Checklist

1. [ ] Update `composer.json` to require PHP 8.1+
2. [ ] Run `composer update yzalis/identicon`
3. [ ] Update namespace imports: `Identicon\` → `Yzalis\Identicon\`
4. [ ] Replace `new Identicon($generator)` with `IdenticonBuilder`
5. [ ] Move parameters from method calls to builder configuration
6. [ ] Update `getColor()` calls to pass the input string
7. [ ] If visual compatibility is needed, add `->useV2Compatibility()`
8. [ ] Run tests to verify behavior
