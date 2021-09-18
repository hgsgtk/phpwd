# phpwd

[![MIT License](https://img.shields.io/github/license/hgsgtk/phpwd)](https://github.com/hgsgtk/phpwd/blob/main/LICENSE)

## Description

The phpwd library is Selenium WebDriver client in PHP.

## Installation

You can install via composer.

```
composer require hgsgtk/phpwd
```

## Getting started

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
