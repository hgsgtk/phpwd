<?php

declare(strict_types=1);

namespace Phpwd\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Phpwd\LocatorStrategy;
use Phpwd\Webdriver;

final class IntegrationTest extends TestCase
{
    public function testGoToExampleCom(): void
    {
        // $this->markTestSkipped('This test case is passed only in local environment which run chromedriver');

        $driver = new Webdriver();

        try {
            // Open browser by webdriver(especially chromedriver)
            $driver->openBrowser();

            // Go to example.com
            $driver->navigateTo('https://example.com/');
            sleep(1); // To demonstration

            // Find the text of title
            $titleElementId = $driver->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $driver->getElementText($titleElementId);
            $this->assertSame('Example Domain', $titleText);

            // Click the link
            $linkElementId = $driver->findElement(LocatorStrategy::LinkText, 'More information...');
            $driver->clickElement($linkElementId);
            sleep(1); // To demonstration

            // Confirm to move IANA
            $titleElementId = $driver->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $driver->getElementText($titleElementId);
            $this->assertSame('IANA-managed Reserved Domains', $titleText);

            // Move to RFC 2606
            $linkElementId = $driver->findElement(LocatorStrategy::Css, '[href="/go/rfc2606"]');
            $driver->clickElement($linkElementId);
            sleep(1); // To demonstration

            $url = $driver->getCurrentUrl();
            $this->assertSame('https://www.rfc-editor.org/rfc/rfc2606.html', $url);

        } finally {
            $driver->closeBrowser();
        }
    }
}
