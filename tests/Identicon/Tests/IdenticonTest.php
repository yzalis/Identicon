<?php

namespace Identicon\Tests;

use Identicon\Generator\ImageMagickGenerator;
use Identicon\Identicon;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
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


    /**
     * @dataProvider resultDataProvider
     */
    public function testResult($string, $imageData)
    {
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }
    public function resultDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAAkklEQVRoge3YwQnAIBAF0WxIYSltS0tplpB/EByWeWdRBj0sVr99Zfr7X3lktzs8kswGBhsYbGCwgcEGhgkNlU9pWBWuO5WajLcT3pINDDYw2MBgA4MNDBNmvmf7jnu/NBMT3pINDDYw2MBgA4MNDBNmvgn3YAODDQw2MNjAYAODDQz+8zHYwGADgw0MNjDYwLAAnSEUgrvPyzUAAAAASUVORK5CYII='),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAAmklEQVRoge3ZwQ2AIBAFUddQmCVYiqVYiiVYmi38A8HJZt6ZbJzAgUhtmec+wpXn9S6etoezyGxgsIHBBgYbGGxg6NAw8uvXL5LP67APNjDYwGADgw0MNjDYwGADgw0MNjDYwGADgw0MBf/Plxj50vUPz+G0DmfJBgYbGGxgsIHBBoYODTV33Nw7X6jDPtjAYAODDQw2MNjA8AG7FBQE2EpVGwAAAABJRU5ErkJggg=='),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAAjUlEQVRoge3Y0QmAMAwAUSMO4AiO5miO5giO4AoRYj3Dve9SerQfobFf55RzrFtyZZXk2eaXjzGCDQw2MNjAYAODDQw2SJIk6Q+i9p/vk906zK02MNjAYAODDQw2MHRoeDDzYUVyHXm87fCWbGCwgcEGBhsYbGBYynccP0R2uAcbGGxgsIHBBgYbGDo03F0LGCmCZDLpAAAAAElFTkSuQmCC'),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAApklEQVRoge3Y0QmFMBAF0ZeHhViKJVmKJVmKpViCV1h1WOZ8h5AxfiwZv8yxb+HKWvOyXq75v3COp9nAYAODDQw2MNjAYAPDqN0uH2+TgTTU4R5sYLCBwQYGGxhsYBhfPeAVmsp3TIa52g/X4V+ygcEGBhsYbGCwgaFDw425tfBlLhSercM92MBgA4MNDDYw2MDQ4Z2vwz3YwGADgw0MNjDYwNCh4QQmpBMQ2jP6OQAAAABJRU5ErkJggg=='),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBCAIAAAABlV4SAAAABnRSTlMAAAAAAABupgeRAAAAm0lEQVRoge3ZwQmAQAwFUSMWZCmWYGmWYCmWZAv/IOsY551DYGAPga0ps59rOHls1+Btc7iLzAYGGxhsYLCBwQaGDg2Vn19YSz46/iANt3V4SzYw2MBgA4MNDDYw/OzmCz17GiY6vCUbGGxgsIHBBgYbGDrcfBXOvZXq//R32MBgA4MNDDYwdGjocPP5t8tgA4MNDDYw2MBgA8MNwagdgwLhJLwAAAAASUVORK5CYII='),
        );
    }

    /**
     * @dataProvider imageMagickResultDataProvider
     */
    public function testImageMagickResult($string, $imageData)
    {
        $this->identicon->setGenerator(new ImageMagickGenerator());
        $this->assertEquals($imageData, $this->identicon->getImageDataUri($string));
    }
    public function imageMagickResultDataProvider()
    {
        return array(
            array('Benjamin', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAQMAAAC0OVsGAAAABlBMVEUAAACAQIBZk25qAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAASAAAAEgARslrPgAAADJJREFUKM9j+P+Hwf4/A///BoYBY0FBAwMDO4j+wcBAZxbCBUDiAePAsBAuGHjWQIQBAEylsffAMPtMAAAAAElFTkSuQmCC'),
            array('8.8.8.8', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAQMAAAC0OVsGAAAABlBMVEUAAACwkEBnpIxVAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAASAAAAEgARslrPgAAADJJREFUKM9jYGD//4Dx/w8GIBgg1v//INb//w0jmwUGQNYfBvv/DPwDyAICIAsK6McCAHgaRw8ODosjAAAAAElFTkSuQmCC'),
            array('8.8.4.4', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAQMAAAC0OVsGAAAABlBMVEUAAABg8OBZBnEqAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAASAAAAEgARslrPgAAADJJREFUKM9j+P+HAQj4/zcwDDxroADQBfb/YW4ZGBYUAMOA/f8Dxv8/QM6iNwsIBpAFAJTD5znOXx4GAAAAAElFTkSuQmCC'),
            array('yzalis', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAQMAAAC0OVsGAAAABlBMVEUAAADgwJA3cC1lAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAASAAAAEgARslrPgAAADFJREFUKM9jYGD/DwQ/GIBg4FkMDPb/GRjozvoPBQ0g1gPGgWX9AbmIn/4s5DAYCBYAaeRVTUt0KIYAAAAASUVORK5CYII='),
            array('benjaminAtYzalisDotCom', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAABBAQMAAAC0OVsGAAAABlBMVEUAAABgsDAvrOz7AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAASAAAAEgARslrPgAAADlJREFUKM9jYGD//4Dx/w8GIBgg1n8oaGD4/4fB/j8DP/1ZCBf8B7lqQFhQFwDDBQigIURH1oDHAgCNSlrmMVCO8AAAAABJRU5ErkJggg=='),
        );
    }
}
