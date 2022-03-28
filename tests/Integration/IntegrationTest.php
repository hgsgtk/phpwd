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
            $linkElement = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $linkElement->getText();
            $this->assertSame('Example Domain', $titleText);

            // Click the link
            $linkElement = $browser->findElement(LocatorStrategy::LinkText, 'More information...');
            $linkElement->click();
            sleep(1); // To demonstration

            // Confirm to move IANA
            $linkElement = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $linkElement->getText();
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

    public function testGoToAutifyCom(): void
    {
        $driver = new Webdriver();

        try {
            // Open browser by webdriver(especially chromedriver)
            $browser = $driver->openBrowser();

            // Go to example.com
            $browser->navigateTo('https://autify.com/');
            sleep(1); // To demonstration

            // Find the text of link
            $linkElement = $browser->findElement(LocatorStrategy::LinkText, 'Why Autify');
            $titleText = $linkElement->getText();
            $this->assertSame('Why Autify', $titleText);

            // Click
            $linkElement->click();
            // Verify if we are in the page why autify
            $this->assertSame('https://autify.com/why-autify', $browser->getCurrentUrl());

            sleep(1); // To demonstration

            $successStories = $browser->findElement(LocatorStrategy::Css, '.container > h3');
            $this->assertSame('Success Stories', $successStories->getText());

            // Go back to the top page
            $browser->back();
            $this->assertSame('https://autify.com/', $browser->getCurrentUrl());

            sleep(1); // To demonstration

            // Fill the company name
            $companyForm = $browser->findElement(LocatorStrategy::Css, '#mce-COMPANY');
            $companyForm->type('Autify');
            sleep(1); // To demonstration

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
