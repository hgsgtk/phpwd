# phpwd

[![MIT License](https://img.shields.io/github/license/hgsgtk/phpwd)](https://github.com/hgsgtk/phpwd/blob/main/LICENSE)

## Description

The phpwd library is Selenium WebDriver client in PHP.

[![Demonstration](https://img.youtube.com/vi/nDI6R5_jSxE/0.jpg)](https://www.youtube.com/watch?v=nDI6R5_jSxE)

## Installation

You can install via composer.

```
composer require hgsgtk/phpwd
```

## Getting started

For example, it can be used like this:

```php
use Phpwd\LocatorStrategy;
use Phpwd\Webdriver;

$driver = new Webdriver();

try {
    // Open browser by webdriver(especially chromedriver)
    $driver->openBrowser();

    // Go to example.com
    $driver->navigateTo('https://example.com/');

    // Find the text of title
    $titleElementId = $driver->findElement(LocatorStrategy::tagName(), 'h1');
    $titleText = $driver->getElementText($titleElementId);
    $this->assertSame('Example Domain', $titleText);

    // Click the link
    $linkElementId = $driver->findElement(LocatorStrategy::linkText(), 'More information...');
    $driver->clickElement($linkElementId);

    // Confirm to move IANA
    $titleElementId = $driver->findElement(LocatorStrategy::tagName(), 'h1');
    $titleText = $driver->getElementText($titleElementId);
    $this->assertSame('IANA-managed Reserved Domains', $titleText);

    // Move to RFC 2606
    $linkElementId = $driver->findElement(LocatorStrategy::css(), '[href="/go/rfc2606"]');
    $driver->clickElement($linkElementId);

    $url = $driver->getCurrentUrl();
    $this->assertSame('https://www.rfc-editor.org/rfc/rfc2606.html', $url);

} finally {
    $driver->closeBrowser();
}
```

## Development

To control a browser, you need to start a remote end (server), which will listen to the commands sent from this library.
By using the included docker-compose.yml, chrome driver can be used immediately.

```
make set-local
```

### Run Test

You can run tests by using Docker environment.

```
make run-tests
```
