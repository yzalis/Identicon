# Identicon generator for PHP

[![Build Status](https://secure.travis-ci.org/yzalis/Identicon.png)](http://travis-ci.org/yzalis/Identicon)

**Identicon** is a library which generate an [identicon](http://en.wikipedia.org/wiki/Identicon) image based on a string.

Here is some examples of awesome results!

![Identicon example #1](doc/benjaminAtYzalisDotCom.png)&nbsp;&nbsp;
![Identicon example #2](doc/Benjamin.png)&nbsp;&nbsp;
![Identicon example #3](doc/8.8.8.8.png)&nbsp;&nbsp;
![Identicon example #4](doc/8.8.4.4.png)&nbsp;&nbsp;
![Identicon example #5](doc/yzalis.png)

## Installation

The recommended way to install Identicon is through composer.

Just create a `composer.json` file for your project:

``` json
{
    "require": {
        "yzalis/identicon": "*"
    }
}
```

And run these two commands to install it:

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
```

Now you can add the autoloader, and you will have access to the library:

``` php
<?php

require 'vendor/autoload.php';
```

If you don't use either **Composer** or a _ClassLoader_ in your application, just require the provided autoloader:

``` php
<?php

require_once 'src/autoload.php';
```

You're done.


## Usage

Images are generated in PNG format with transparent background.

The string can be an email, an IP address, a username, an ID or something else.

### Generate an identicon

Create a new ```Identicon``` object.

``` php
$identicon = new Identicon();
```

Then you can generate and display an identicon image

``` php
$identicon->displayImage('foo');
```

or generate and get the image data

``` php
$imageData = $identicon->getImageData('bar');
```

or generate and get the base 64 image uri ready for integrate into an HTML img tag.

``` php
$imageDataUri = $identicon->getImageDataUri('bar');
```
``` html
<img src="<?php echo $imageDataUri; ?>" alt="bar Identicon" />
```


### Change the size

By default the size will be 64 pixels. If you want to change the image size just add a secondary parameter. 512 x 512px in this example.

``` php
$identicon->displayImage('foo', 512);
```

### Color

The color is automaticaly generated according to the string hash but you can chose to specify a color by adding a third argument.

Color can be an hexadecimal with 6 characters

``` php
$identicon->displayImage('bar', 64, 'A87EDF');
```

or an array with red, green, blue value

``` php
$identicon->displayImage('foo', 64, array(200, 100, 150));
```

That's it!

## Unit Tests

To run unit tests, you'll need and a set of dependencies you can install using Composer:

```
php composer.phar install
```

Once installed, just launch the following command:

```
phpunit
```

Everythings should be ok.


## Credits

* Benjamin Laugueux <benjamin@laugueux.org>
* [All contributors](https://github.com/yzalis/Identicon)

Inspired by Github [blog post](https://github.com/blog/1586-identicons) about Identicon.


## License

Identicon is released under the MIT License. See the bundled LICENSE file for details.