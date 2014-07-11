<?php

namespace Identicon;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 * @author Francis Chuang <francis.chuang@gmail.com>
 */
class Identicon
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var integer
     */
    private $color;

    /**
     * @var integer
     */
    private $size;

    /**
     * An array containing 3 elements representing r, g and b
     *
     * @var array
     */
    private $backgroundColor = 'none';

    /**
     * @var integer
     */
    private $pixelRatio;

    /**
     * @var string
     * Uses GD by default, because the library has always used GD.
     */
    private $engine = 'gd';

    /**
     * @var array
     */
    private $arrayOfSquare = array();

    /**
     * Sets the engine to be used when generating the identicon
     *
     * @param string $engine
     *
     * @throws \Exception
     */
    public function setEngine($engine)
    {
        if($engine == 'gd'){

            if (!extension_loaded('gd')) {
                throw new \Exception('GD does not appear to be avaliable in your PHP installation. Please try another engine');
            }

            $this->engine = $engine;

        }elseif($engine == 'imagemagick'){

            if (!extension_loaded('imagick')) {
                throw new \Exception('ImageMagick does not appear to be avaliable in your PHP installation. Please try another engine');
            }

            $this->engine = $engine;
        }else{
            throw new \Exception('Engine should be either "gd" or "imagemagick".');
        }
    }

    /**
     * Set the image size
     *
     * @param integer $size
     *
     * @return Identicon
     */
    public function setSize($size)
    {
        $this->size = $size;
        $this->pixelRatio = round($size / 5);

        return $this;
    }

    /**
     * Get the image size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Generate a hash fron the original string
     *
     * @param string $string
     *
     * @return Identicon
     */
    public function setString($string)
    {
        if (null === $string) {
            throw new \Exception('The string cannot be null.');
        }

        $this->hash = md5($string);

        $this->convertHashToArrayOfBoolean();

        return $this;
    }

    /**
     * Get the identicon string hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Convert the hash into an multidimensionnal array of boolean
     *
     * @return Identicon
     */
    private function convertHashToArrayOfBoolean()
    {
        preg_match_all('/(\w)(\w)/', $this->hash, $chars);
        foreach ($chars[1] as $i => $char) {
            if ($i % 3 == 0) {
                $this->arrayOfSquare[$i/3][0] = $this->convertHexaToBoolean($char);
                $this->arrayOfSquare[$i/3][4] = $this->convertHexaToBoolean($char);
            } elseif ($i % 3 == 1) {
                $this->arrayOfSquare[$i/3][1] = $this->convertHexaToBoolean($char);
                $this->arrayOfSquare[$i/3][3] = $this->convertHexaToBoolean($char);
            } else {
                $this->arrayOfSquare[$i/3][2] = $this->convertHexaToBoolean($char);
            }
            ksort($this->arrayOfSquare[$i/3]);
        }

        $this->color[0] = hexdec(array_pop($chars[1]))*16;
        $this->color[1] = hexdec(array_pop($chars[1]))*16;
        $this->color[2] = hexdec(array_pop($chars[1]))*16;

        return $this;
    }

    /**
     * Convert an heaxecimal number into a boolean
     *
     * @param string $hexa
     *
     * @return boolean
     */
    private function convertHexaToBoolean($hexa)
    {
        return (bool) intval(round(hexdec($hexa)/10));
    }

    /**
     *
     *
     * @return array
     */
    public function getArrayOfSquare()
    {
        return $this->arrayOfSquare;
    }

    /**
     * Generate the Identicon image
     *
     * @param string  $string
     * @param integer $size
     * @param string $hexaColor
     * @param string $backgroundColor
     */
    private function generateImage($string, $size, $color, $backgroundColor)
    {
        $this->setString($string);
        $this->setSize($size);

        // prepage the color
        if (null !== $color) {
            $this->setColor($color);
        }

        if (null !== $backgroundColor) {
            $this->setBackgroundColor($backgroundColor);
        }

        if($this->engine == 'gd'){
            return $this->generateWithGD();
        }elseif($this->engine == 'imagemagick'){
            return $this->generateWithImageMagick();
        }else{
            throw new \Exception("{$this->engine} is not a valid engine for generating the image.");
        }
    }

    private function generateWithImageMagick()
    {
        $image = new \Imagick();

        $background = 'none';

        if($this->backgroundColor !== 'none'){
            $background = new \ImagickPixel("rgb({$this->backgroundColor[0]},{$this->backgroundColor[1]},{$this->backgroundColor[2]})");
        }

        $image->newImage($this->pixelRatio * 5, $this->pixelRatio * 5, $background, 'png');

        $color = new \ImagickPixel("rgb({$this->color[0]},{$this->color[1]},{$this->color[2]})");

        $draw = new \ImagickDraw();
        $draw->setFillColor($color);

        // draw the content
        foreach ($this->arrayOfSquare as $lineKey => $lineValue) {
            foreach ($lineValue as $colKey => $colValue) {
                if (true === $colValue) {
                    $draw->rectangle( $colKey * $this->pixelRatio, $lineKey * $this->pixelRatio, ($colKey + 1) * $this->pixelRatio, ($lineKey + 1) * $this->pixelRatio);
                }
            }
        }

        $image->drawImage($draw);

        return $image;
    }

    private function generateWithGD()
    {
        // prepare the image
        $image = imagecreatetruecolor($this->pixelRatio * 5, $this->pixelRatio * 5);

        if($this->backgroundColor === 'none'){
            $background = imagecolorallocate($image, 0, 0, 0);
            imagecolortransparent($image, $background);
        }else{
            $background = imagecolorallocate($image, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
            imagefill($image, 0,0, $background);
        }

        $color = imagecolorallocate($image, $this->color[0], $this->color[1], $this->color[2]);

        // draw the content
        foreach ($this->arrayOfSquare as $lineKey => $lineValue) {
            foreach ($lineValue as $colKey => $colValue) {
                if (true === $colValue) {
                    imagefilledrectangle($image, $colKey * $this->pixelRatio, $lineKey * $this->pixelRatio, ($colKey + 1) * $this->pixelRatio, ($lineKey + 1) * $this->pixelRatio, $color);
                }
            }
        }

        return $image;
    }

    /**
     * Get the raw binary data
     *
     * @param mixed $image
     */
    private function getImageBinaryData($image)
    {

        if($this->engine == 'gd'){
            imagepng($image);
        }elseif($this->engine == 'imagemagick'){
            echo $image;
        }
    }

    /**
     * Set the image color
     *
     * @param string|array $color The color in hexa (6 chars) or rgb array
     *
     * @return Identicon
     */
    public function setColor($color)
    {
        if (is_array($color)) {
            $this->color[0] = $color[0];
            $this->color[1] = $color[1];
            $this->color[2] = $color[2];
        } else {
            if (false !== strpos($color, '#')) {
                $color = substr($color, 1);
            }
            $this->color[0] = hexdec(substr($color, 0, 2));
            $this->color[1] = hexdec(substr($color, 2, 2));
            $this->color[2] = hexdec(substr($color, 4, 2));
        }

        return $this;
    }

    /**
     * Set the background color
     *
     * @param string|array $backgroundColor The color in hexa (6 chars) or rgb array
     *
     * @return Identicon
     */
    public function setBackgroundColor($backgroundColor)
    {
        if($backgroundColor === 'none'){
            $this->backgroundColor = 'none';

        }elseif (is_array($backgroundColor)) {
            $this->backgroundColor = array();
            $this->backgroundColor[0] = $backgroundColor[0];
            $this->backgroundColor[1] = $backgroundColor[1];
            $this->backgroundColor[2] = $backgroundColor[2];
        } else {
            if (false !== strpos($backgroundColor, '#')) {
                $backgroundColor = substr($backgroundColor, 1);
            }

            $this->backgroundColor = array();
            $this->backgroundColor[0] = hexdec(substr($backgroundColor, 0, 2));
            $this->backgroundColor[1] = hexdec(substr($backgroundColor, 2, 2));
            $this->backgroundColor[2] = hexdec(substr($backgroundColor, 4, 2));
        }

        return $this;
    }

    /**
     * Get the color
     *
     * @return arrray
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Returns the handle (imagemagick or GD resource) for further processing by the user
     *
     * @param string $string
     * @param integer $size
     * @param string $hexaColor
     * @param string $backgroundColor
     *
     * @return resource
     */
    public function getImageHandle($string, $size = 64, $hexaColor = null, $backgroundColor = null)
    {
        return $this->generateImage($string, $size, $hexaColor, $backgroundColor);
    }

    /**
     * Display an Identicon image
     *
     * @param string  $string
     * @param integer $size
     * @param string $hexaColor
     * @param string $backgroundColor
     */
    public function displayImage($string, $size = 64, $hexaColor = null, $backgroundColor = null)
    {
        header("Content-Type: image/png");
        $image = $this->generateImage($string, $size, $hexaColor, $backgroundColor);

        $this->getImageBinaryData($image);
    }

    /**
     * Get an Identicon PNG image data
     *
     * @param string  $string
     * @param integer $size
     * @param string $hexaColor
     * @param string $backgroundColor
     *
     * @return string
     */
    public function getImageData($string, $size = 64, $hexaColor = null, $backgroundColor = null)
    {
        ob_start();
        $image = $this->generateImage($string, $size, $hexaColor, $backgroundColor);

        $this->getImageBinaryData($image);

        $imageData = ob_get_contents();
        ob_end_clean();

        return $imageData;
    }

    /**
     * Get an Identicon PNG image data
     *
     * @param string  $string
     * @param integer $size
     * @param string $hexaColor
     * @param string $backgroundColor
     *
     * @return string
     */
    public function getImageDataUri($string, $size = 64, $hexaColor = null, $backgroundColor = null)
    {
        return sprintf('data:image/png;base64,%s', base64_encode($this->getImageData($string, $size, $hexaColor, $backgroundColor)));
    }
}
