<?php

namespace Identicon\Tests;

use Identicon\Generator\BaseGenerator;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class BaseGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $faker;
    protected $identicon;

    protected function setUp()
    {
        $this->faker = \Faker\Factory::create();
        $this->generator = new BaseGenerator();
    }

    public function testHash()
    {
        for ($i = 0; $i < 50; $i++) {
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
        for ($i = 0; $i < 50; $i++) {
            $this->generator->setString($this->faker->email);
            foreach ($this->generator->getArrayOfSquare() as $lineKey => $lineValue) {
                $this->assertContainsOnly('boolean', $lineValue, true);
            }
        }
    }

    /**
     * @dataProvider testColorsDataProvider
     */
    public function testColors($color, $expected)
    {
        $this->assertEquals($expected, $this->generator->setBackgroundColor($color)->getBackgroundColor());
        $this->assertEquals($expected, $this->generator->setColor($color)->getColor());
    }
    public function testColorsDataProvider()
    {
        return array(
            array('#ffffff', array(255, 255, 255)),
            array('000000', array(0, 0, 0)),
            array(array(0, 0, 0), array(0, 0, 0)),
            array(array(255, 255, 255), array(255, 255, 255)),
        );
    }
}
