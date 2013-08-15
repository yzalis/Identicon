<?php

namespace Identicon\Tests;

use Identicon\Identicon;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class IdenticonTest extends \PHPUnit_Framework_TestCase
{
    protected $faker;

    protected function setUp()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function testHash()
    {
        $identicon = new Identicon();
        for ($i = 0; $i < 50; $i++) {
            // Get the previous hash
            $previousHash = $identicon->getHash();

            // Set a new string
            $identicon->setString($this->faker->email);

            // Test the hash length
            $this->assertEquals(32, strlen($identicon->getHash()));

            // Test the hash generation result
            $this->assertThat(
                $identicon->getHash(),
                $this->logicalNot(
                    $this->equalTo($previousHash)
                )
            );
        }
    }

    public function testArrayOfSquare()
    {
        $identicon = new Identicon();
        for ($i = 0; $i < 50; $i++) {
            $identicon->setString($this->faker->email);
            foreach ($identicon->getArrayOfSquare() as $lineKey => $lineValue) {
                $this->assertContainsOnly('boolean', $lineValue, true);
            }
        }
    }
}