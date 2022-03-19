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
        $driver = new Webdriver();

        try {
            // Open browser by webdriver(especially chromedriver)
            $browser = $driver->openBrowser();

            // Go to example.com
            $browser->navigateTo('https://example.com/');
            sleep(1); // To demonstration

            // Find the text of title
            $titleElementId = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $browser->getElementText($titleElementId);
            $this->assertSame('Example Domain', $titleText);

            // Click the link
            $linkElementId = $browser->findElement(LocatorStrategy::LinkText, 'More information...');
            $browser->clickElement($linkElementId);
            sleep(1); // To demonstration

            // Confirm to move IANA
            $titleElementId = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $browser->getElementText($titleElementId);
            $this->assertSame('IANA-managed Reserved Domains', $titleText);

            // Move to RFC 2606
            $linkElementId = $browser->findElement(LocatorStrategy::Css, '[href="/go/rfc2606"]');
            $browser->clickElement($linkElementId);
            sleep(1); // To demonstration

            $url = $browser->getCurrentUrl();
            $this->assertSame('https://www.rfc-editor.org/rfc/rfc2606.html', $url);

        } finally {
            $browser->closeBrowser();
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOpenMultipleBrowsers(): void
    {
        $driver = new Webdriver();

        try {
            // Open browser by webdriver(especially chromedriver)
            $browser1 = $driver->openBrowser();
            $browser2 = $driver->openBrowser();

        } finally {
            $browser1->closeBrowser();
            $browser2->closeBrowser();
        }
    }
}
