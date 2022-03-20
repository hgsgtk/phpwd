# phpwd

[![MIT License](https://img.shields.io/github/license/hgsgtk/phpwd)](https://github.com/hgsgtk/phpwd/blob/main/LICENSE)

## Description

The phpwd library is Selenium WebDriver client in PHP.

[![Demonstration](https://img.youtube.com/vi/nDI6R5_jSxE/0.jpg)](https://www.youtube.com/watch?v=nDI6R5_jSxE)

## Installation

You can install via composer.

```bash
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
    $driver->closeBrowser();
}
```

## Development

To control a browser, you need to start a remote end (server), which will listen to the commands sent from this library.
By using the included docker-compose.yml, chrome driver can be used immediately.

```bash
make set-local
```

### Run Test

You can run tests by using Docker environment.

```bash
make run-tests
```
