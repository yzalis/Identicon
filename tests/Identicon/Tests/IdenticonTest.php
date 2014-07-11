<?php

namespace Identicon\Tests;

use Identicon\Identicon;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 * @author Francis Chuang <francis.chuang@gmail.com>
 */
class IdenticonTest extends \PHPUnit_Framework_TestCase
{
    protected $faker;
    protected $identicon;

    protected function setUp()
    {
        $this->faker = \Faker\Factory::create();
        $this->identicon = new Identicon();
    }

    public function testHash()
    {
        for ($i = 0; $i < 50; $i++) {
            // Get the previous hash
            $previousHash = $this->identicon->getHash();

            // Set a new string
            $this->identicon->setString($this->faker->email);

            // Test the hash length
            $this->assertEquals(32, strlen($this->identicon->getHash()));

            // Test the hash generation result
            $this->assertThat(
                $this->identicon->getHash(),
                $this->logicalNot(
                    $this->equalTo($previousHash)
                )
            );
        }
    }

    public function testArrayOfSquare()
    {
        for ($i = 0; $i < 50; $i++) {
            $this->identicon->setString($this->faker->email);
            foreach ($this->identicon->getArrayOfSquare() as $lineKey => $lineValue) {
                $this->assertContainsOnly('boolean', $lineValue, true);
            }
        }
    }

    /**
     * @dataProvider testColorDataProvider
     */
    public function testColor($color, $expected)
    {
        $this->assertEquals($expected, $this->identicon->setColor($color)->getColor());
    }
    public function testColorDataProvider()
    {
        return array(
            array('#ffffff', array(255, 255, 255)),
            array('000000', array(0, 0, 0)),
            array(array(0, 0, 0), array(0, 0, 0)),
            array(array(255, 255, 255), array(255, 255, 255)),
        );
    }

    /**
     * @dataProvider resultWithGDDataProvider
     */
    public function testResultWithGD($string, $imageData)
    {
        $this->identicon->setEngine('gd');
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }

    public function resultWithGDDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAkklEQVRoge3YwQnAIBAF0WxIYSltS0tplpB/EByWeWdRBj0sVr99Zfr7X3lktzs8kswGBhsYbGCwgcEGhgkNlU9pWBWuO5WajLcT3pINDDYw2MBgA4MNDBNmvmf7jnu/NBMT3pINDDYw2MBgA4MNDBNmvgn3YAODDQw2MNjAYAODDQz+8zHYwGADgw0MNjDYwLAAnSEUgrvPyzUAAAAASUVORK5CYII='),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAmklEQVRoge3ZwQ2AIBAFUddQmCVYiqVYiiVYmi38A8HJZt6ZbJzAgUhtmec+wpXn9S6etoezyGxgsIHBBgYbGGxg6NAw8uvXL5LP67APNjDYwGADgw0MNjDYwGADgw0MNjDYwGADgw0MBf/Plxj50vUPz+G0DmfJBgYbGGxgsIHBBoYODTV33Nw7X6jDPtjAYAODDQw2MNjA8AG7FBQE2EpVGwAAAABJRU5ErkJggg=='),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAjUlEQVRoge3Y0QmAMAwAUSMO4AiO5miO5giO4AoRYj3Dve9SerQfobFf55RzrFtyZZXk2eaXjzGCDQw2MNjAYAODDQw2SJIk6Q+i9p/vk906zK02MNjAYAODDQw2MHRoeDDzYUVyHXm87fCWbGCwgcEGBhsYbGBYynccP0R2uAcbGGxgsIHBBgYbGDo03F0LGCmCZDLpAAAAAElFTkSuQmCC'),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAApklEQVRoge3Y0QmFMBAF0ZeHhViKJVmKJVmKpViCV1h1WOZ8h5AxfiwZv8yxb+HKWvOyXq75v3COp9nAYAODDQw2MNjAYAPDqN0uH2+TgTTU4R5sYLCBwQYGGxhsYBhfPeAVmsp3TIa52g/X4V+ygcEGBhsYbGCwgaFDw425tfBlLhSercM92MBgA4MNDDYw2MDQ4Z2vwz3YwGADgw0MNjDYwNCh4QQmpBMQ2jP6OQAAAABJRU5ErkJggg=='),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAm0lEQVRoge3ZwQmAQAwFUSMWZCmWYGmWYCmWZAv/IOsY551DYGAPga0ps59rOHls1+Btc7iLzAYGGxhsYLCBwQaGDg2Vn19YSz46/iANt3V4SzYw2MBgA4MNDDYw/OzmCz17GiY6vCUbGGxgsIHBBgYbGDrcfBXOvZXq//R32MBgA4MNDDYwdGjocPP5t8tgA4MNDDYw2MBgA8MNwagdgwLhJLwAAAAASUVORK5CYII='),
        );
    }

    /**
     * @dataProvider resultWithGDWithBackgroundDataProvider
     */
    public function testResultWithGDWithBackground($string, $imageData)
    {
        $this->identicon->setEngine('gd');
        $this->identicon->setBackgroundColor('#6FC6F5');
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }

    public function resultWithGDWithBackgroundDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAApUlEQVRoge3YoQ2AQAxAUY6wABuxEQqDOoVhL9ZB4RmBiob8NP/py5EfTjRtfelDzH1sn2fm/fz/tjH4STIbGGxgsIHBBgYbGCo0tPjMh9XW64mciw+kuSLjbYW3ZAODDQw2MNjAYANDhZlvSr8xd6UZUeEt2cBgA4MNDDYw2MCQPy/9v02r8B9sYLCBwQYGGxhsYLCBwT0fgw0MNjDYwGADgw0ML8B6FoWxN2sSAAAAAElFTkSuQmCC'),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAoUlEQVRoge3ZsQmAMBBA0VMyk721wzidtZ2F+9i7wgmSfI7/6nDkkxRBp/16ImG9t8yyiDiXo/O0OTmLzAYGGxhsYLCBwQaGCg0t//waIrO9CudgA4MNDDYw2MBgA4MNDDYw2MBgA4MNDDYw2MDQfp/Y/8Phh4b+P56T0yrcJRsYbGCwgcEGBhsYKjS0zNtrlOTeKpyDDQw2MNjAYAODDQwvRPUWCrU0K+cAAAAASUVORK5CYII='),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAApklEQVRoge3ZoQ2AMBQGYSD1KJZgKqZG4zGwASv8osDl5T7dNL0U8VLG7TqGzLwv4cpe7vVMlk1vn+MDNjDYwGADgw0MNjDYwNC+n0Zz4dkq3IMNDDYw2MBgA4MNDC18Sxuy6eWX3Srcgw0MNjDYwGADgw0MFRrG/N8uVvrORx5vK3xLNjDYwGADgw0MNjC07jvms1AvFe7BBgYbGGxgsIHBBoYKDQ91gxrjvx41lgAAAABJRU5ErkJggg=='),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAsklEQVRoge3YMQ6CQBBGYZYQb2pNPAn2XoaKc3gJGiqO4G9c9WXyvnpD9mUoJtvmbR8Ct+ORHOtuuVxfnhl/cI9vs4HBBgYbGGxgsIHBBoYW7q2hfL1NFtJQhTnYwGADgw0MNjDYwNCe6/3fd/jU1P2LyTLX9+Wzwr9kA4MNDDYw2MBgA0OFhjf21o4vc6FwNawwBxsYbGCwgcEGBhsYKrzzVZiDDQw2MNjAYAODDQwVGk5YWBdkDQz/hwAAAABJRU5ErkJggg=='),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAvUlEQVRoge3ZsQ3CMBQGYQe5p2MOepbJBqzGEjAMHUV6iizwR1j2Yd1XW08+ycWTvNxfWwl83rfkWCnlfHl2nnYKZ5HZwGADgw0MNjDYwDBDw7I+rqPv8KuaH+2/kIbTZnhLNjDYwGADgw0MNjAc2JfyXajztAMNobarYWKGt2QDgw0MNjDYwGADQ/t9qe0ulKjJilZG3Gzn//T/sIHBBgYbGGxgmKGhjlqEQsn1/NtlsIHBBgYbGGxgsIHhC6G+J1GNv/eNAAAAAElFTkSuQmCC'),
        );
    }

    /**
     * @dataProvider resultWithImageMagickDataProvider
     */
    public function testResultWithImageMagick($string, $imageData)
    {
        $this->identicon->setEngine('gd');
        $this->identicon->setEngine('imagemagick');
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }

    public function resultWithImageMagickDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEUAAACAQID///8oxzV4AAAAAXRSTlMAQObYZgAAAAFiS0dEAmYLfGQAAAA7SURBVDjLYwgNDQ1gYGAFUgwMDEDSgWEYi6AAB6AEgygqd3iIYPoUQocwMDAORxFMn44ckcEWFxSLAADggxzqrnNbEwAAAABJRU5ErkJggg=='),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEUAAACwkED////TBaOKAAAAAXRSTlMAQObYZgAAAAFiS0dEAmYLfGQAAAA8SURBVDjLY2BgYBANDQ1hYGAMDQ11YICAYSkSCgZQEbDgqMjwEEEAsEgAAwMrkIJE+fAVgQCoCBIY4iIAiYPVHLGRsGgAAAAASUVORK5CYII='),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEUAAABg8OD///8VYtHtAAAAAXRSTlMAQObYZgAAAAFiS0dEAmYLfGQAAAA3SURBVDjLYwgNDQ1ggAEgx4Fh5IgMVwDxKSuQQvX7sBRBAeA4FQ0NDWFgYESK4mEjguzNYSoCAPPUns36/EyfAAAAAElFTkSuQmCC'),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEUAAADgwJD////BzN0vAAAAAXRSTlMAQObYZgAAAAFiS0dEAmYLfGQAAAA3SURBVDjLY2BgYBANhQEHBggYOSJAwArkMqCAoS4SigYcoCIhDAyMI0AkgAEpIIaRCNY4HXYiANgo4pIdEPUxAAAAAElFTkSuQmCC'),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEUAAABgsDD///9rxTA9AAAAAXRSTlMAQObYZgAAAAFiS0dEAmYLfGQAAABFSURBVDjLY2BgYBANDQ1hYGAMDQ11YICAYSkSigYcQCIBDAysQAoiPYxEMH0aCg+NYSeC7FNIvCNzh4cI1jgdbKmOGiIApgPozDIIZX8AAAAASUVORK5CYII='),
        );
    }

    /**
     * @dataProvider resultWithImageMagickWithBackgroundDataProvider
     */
    public function testResultWithImageMagickWithBackground($string, $imageData)
    {
        $this->identicon->setEngine('imagemagick');
        $this->identicon->setBackgroundColor('#6FC6F5');
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }

    public function resultWithImageMagickWithBackgroundDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEWAQIBvxvX///9qTKHjAAAAAWJLR0QCZgt8ZAAAADVJREFUOMtjYGBgYA0NDQBSoaGhDFAwXEXQAFDGAc4RBXGGhwguwBgaGjIcRUYyGGxxQbEIAL2wTLcerAFMAAAAAElFTkSuQmCC'),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEVvxvWwkED////aULYxAAAAAWJLR0QCZgt8ZAAAADxJREFUOMtjYGBgEA0NDWFgYAwNDXVggIBhKRIKBlARsOCoyPAQQQCwSAADAyuQgkT58BWBAKgIEhjiIgCJg9UcsZGwaAAAAABJRU5ErkJggg=='),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEVg8OBvxvX///8NOrpKAAAAAWJLR0QCZgt8ZAAAADlJREFUOMtjYGBgYA2FAQYoGCEioWjAYZiIQHwaAPEhkt+HpQgagAQAY2hoCJAjCg2fYSMCBcNZBACqX8rFtjZNcAAAAABJRU5ErkJggg=='),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEVvxvXgwJD////ImciUAAAAAWJLR0QCZgt8ZAAAADdJREFUOMtjYGBgEA2FAQcGCBg5IkDACuQyoIChLhKKBhygIiEMDIwjQCSAASkghpEI1jgddiIA2Cjikh0Q9TEAAAAASUVORK5CYII='),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAgMAAADzmSHWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACVBMVEVvxvVgsDD///9ikCWGAAAAAWJLR0QCZgt8ZAAAAEVJREFUOMtjYGBgEA0NDWFgYAwNDXVggIBhKRKKBhxAIgEMDKxACiI9jEQwfRoKD41hJ4LsU0i8I3OHhwjWOB1sqY4aIgCmA+jMMghlfwAAAABJRU5ErkJggg=='),
        );
    }
}