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
            $whyAutify = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $whyAutify->getText();
            $this->assertSame('Example Domain', $titleText);

            // Click the link
            $whyAutify = $browser->findElement(LocatorStrategy::LinkText, 'More information...');
            $whyAutify->click();
            sleep(1); // To demonstration

            // Confirm to move IANA
            $whyAutify = $browser->findElement(LocatorStrategy::TagName, 'h1');
            $titleText = $whyAutify->getText();
            $this->assertSame('IANA-managed Reserved Domains', $titleText);

            // Move to RFC 2606
            $whyAutify = $browser->findElement(LocatorStrategy::Css, '[href="/go/rfc2606"]');
            $whyAutify->click();
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
            $whyAutify = $browser->findElement(LocatorStrategy::PartialLinkText, 'Why Auti');
            $titleText = $whyAutify->getText();
            $this->assertSame('Why Autify', $titleText);

            // Click
            $whyAutify->click();
            // Verify if we are in the page why autify
            $this->assertSame('https://autify.com/why-autify', $browser->getCurrentUrl());

            sleep(1); // To demonstration

            $successStories = $browser->findElement(LocatorStrategy::Css, '.container > h3');
            $this->assertSame('Success Stories', $successStories->getText());

            // Go back to the top page
            $browser->back();
            $this->assertSame('https://autify.com/', $browser->getCurrentUrl());

            sleep(1); // To demonstration

            // Fill an applicant information
            $firstName = $browser->findElement(LocatorStrategy::Css, '#mce-FNAME');
            $firstName->type('James');

            $lastName = $browser->findElement(LocatorStrategy::Css, '#mce-LNAME');
            $lastName->type('William');

            $email = $browser->findElement(LocatorStrategy::Css, '#mce-EMAIL');
            $email->type('xxxxx@xxxxx.com');

            $companyForm = $browser->findElement(LocatorStrategy::Css, '#mce-COMPANY');
            $companyForm->type('Autify');
            sleep(2); // To demonstration

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
