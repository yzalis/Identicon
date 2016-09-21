<?php

namespace Identicon\Tests;

use Identicon\Generator\BaseGenerator;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class BaseGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $faker;
    protected $generator;

    protected function setUp()
    {
        $this->faker = \Faker\Factory::create();
        $this->generator = new BaseGenerator();
    }

    public function testHash()
    {
        for ($i = 0; $i < 50; ++$i) {
            // Get the previous hash
            $previousHash = $this->generator->getHash();

            // Set a new string
            $this->generator->setString($this->faker->email);

            // Test the hash length
            $this->assertEquals(32, strlen($this->generator->getHash()));

            // Test the hash generation result
            $this->assertThat(
                $this->generator->getHash(),
                $this->logicalNot(
                    $this->equalTo($previousHash)
                )
            );
        }
    }

    public function testArrayOfSquare()
    {
        for ($i = 0; $i < 50; ++$i) {
            $this->generator->setString($this->faker->email);
            foreach ($this->generator->getArrayOfSquare() as $lineKey => $lineValue) {
                $this->assertContainsOnly('boolean', $lineValue, true);
            }
        }
    }

    /**
     * @dataProvider testColorsDataProvider
     *
     * @param string $color
     * @param array  $expected
     */
    public function testColors($color, $expected)
    {
        $this->assertEquals($expected, $this->generator->setBackgroundColor($color)->getBackgroundColor());
        $this->assertEquals($expected, $this->generator->setColor($color)->getColor());
    }

    public function testColorsDataProvider()
    {
        return [
            ['#ffffff', [255, 255, 255]],
            ['ffffff', [255, 255, 255]],
            ['#000000', [0, 0, 0]],
            ['000000', [0, 0, 0]],
            ['#fff', [255, 255, 255]],
            ['fff', [255, 255, 255]],
            ['#000', [0, 0, 0]],
            ['000', [0, 0, 0]],
            ['#f0f', [255, 0, 255]],
            ['f0f', [255, 0, 255]],
            ['#0f0', [0, 255, 0]],
            ['0f0', [0, 255, 0]],
            ['111', [17, 17, 17]],
            [[0, 0, 0], [0, 0, 0]],
            [[255, 255, 255], [255, 255, 255]],
        ];
    }
}
