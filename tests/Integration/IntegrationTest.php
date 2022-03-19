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
            $titleElement = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $titleElement->getText();
            $this->assertSame('Example Domain', $titleText);

            // Click the link
            $linkElement = $browser->findElement(LocatorStrategy::LinkText, 'More information...');
            $linkElement->click();
            sleep(1); // To demonstration

            // Confirm to move IANA
            $titleElement = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $titleElement->getText();
            $this->assertSame('IANA-managed Reserved Domains', $titleText);

            // Move to RFC 2606
            $linkElement = $browser->findElement(LocatorStrategy::Css, '[href="/go/rfc2606"]');
            $linkElement->click();
            sleep(1); // To demonstration

            $url = $browser->getCurrentUrl();
            $this->assertSame('https://www.rfc-editor.org/rfc/rfc2606.html', $url);

        } finally {
            $browser->close();
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
            $browser1->close();
            $browser2->close();
        }
    }
}
