<?php

declare(strict_types=1);

namespace Phpwd\Tests\Integration;

use Phpwd\LocatorStrategy;
use Phpwd\Webdriver;

/**
 * The test class is expected to run on docker-compose network.
 */
final class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function testGoToExampleCom(): void
    {
        $this->markTestSkipped('This test passes only in local environment now.');

        $driver = new Webdriver();

        try {
            $driver->openBrowser();

            $driver->goto('https://example.com');
            sleep(1); // To demonstration

            $titleElementId = $driver->findElement(LocatorStrategy::tagName(), 'h1');
            $titleText = $driver->getElementText($titleElementId);
            $this->assertSame('Example Domain', $titleText);
        } finally {
            $driver->closeBrowser();
        }

        $this->assertTrue(true, "initialize project");
    }
}
