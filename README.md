# phpwd

## Description

The phpwd library is PHP language binding for Selenium WebDriver client.

## Installation

You can install via composer.

```
composer require hgsgtk/phpwd
```

## Getting started

To control a browser, you need to start a remote end (server), which will listen to the commands sent from this library.
By using the included docker-compose.yml, chrome driver can be used immediately.

```
make set-local
```

## Development

### Run Test

You can run tests by using Docker environment.

```
make run-tests
```
