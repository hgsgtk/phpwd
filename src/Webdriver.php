<?php

declare(strict_types=1);

namespace Phpwd;

use Phpwd\Exceptions\BadMethodCallException;
use Phpwd\Exceptions\InvalidResponseException;
use Phpwd\Exceptions\LogicException;

/**
 * It supports only chromedriver.
 */
final class Webdriver
{
    /**
     * @var string|null sessionId
     * @link https://www.w3.org/TR/webdriver/#new-session
     */
    private string|null $sessionId = null;

    private ClientInterface $client;

    /**
     * @param string|null $remoteEndUrl WebDriver remote end url
     * @link https://w3c.github.io/webdriver/#nodes
     */
    public function __construct(?string $remoteEndUrl = null)
    {
        $remoteEndUrl = $remoteEndUrl ?? 'http://localhost:9515';

        $this->client = new HttpClient($remoteEndUrl);
    }

    public function openBrowser()
    {
        if ($this->sessionId) {
            // TODO: support to open multiple browsers
            throw new BadMethodCallException('it does not support opening multiple browser by one Webdriver class.');
        }

        // To support another drivers, we should prepare option for capabilities setting.
        // It may be better to create Option class which defined the default option.
        // TODO: support Selenium server.
        $response = $this->client->post('/session', [
            'capabilities' => [
                'alwaysMatch' => [
                    'goog:chromeOptions' => [
                        'args' => [
                            '--no-sandbox'
                        ]
                    ]
                ]
            ]
        ]);

        if (!array_key_exists('value', $response) ||
            !array_key_exists('sessionId', $response['value'])) {
            throw new InvalidResponseException('Response from POST /session is invalid: ' . var_export($response, true));
        }

        $this->sessionId = $response['value']['sessionId'];
    }

    public function closeBrowser()
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $this->client->delete('/session/' . $this->sessionId);
    }

    public function navigateTo(string $url): void
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $this->client->post('/session/' . $this->sessionId . '/url', [
            'url' => $url
        ]);
    }

    /**
     * @param LocatorStrategy $locatorStrategy
     * @param string $value
     * @return ElementId elementId
     *
     * @link https://www.w3.org/TR/webdriver/#find-element
     */
    public function findElement(LocatorStrategy $locatorStrategy, string $value): ElementId
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $response = $this->client->post('/session/' . $this->sessionId . '/element', [
            'using' => $locatorStrategy,
            'value' => $value,
        ]);

        /**
         * Response format is like this.
         *
         * {
         *  "value": {
         *      "element-6066-11e4-a52e-4f735466cecf": "84b10d39-94f5-4768-8457-dd218597a1e5"
         *  }
         * }
         */
        if (!array_key_exists('value', $response) ||
            count($response['value']) === 0)  {
            throw new InvalidResponseException('response is invalid: ' . var_export($response, true));
        }

        return new ElementId((string)array_values($response['value'])[0]);
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#dfn-get-element-text
     *
     * @param ElementId $elementId
     * @return string
     */
    public function getElementText(ElementId $elementId): string
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $response = $this->client->get('/session/' . $this->sessionId . '/element/' . $elementId->toString() . '/text');

        if (!array_key_exists('value', $response) ||
            !is_string($response['value']))  {
            throw new InvalidResponseException('response is invalid: ' . var_export($response, true));
        }

        return $response['value'];
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#element-click
     *
     * @param ElementId $elementId
     */
    public function clickElement(ElementId $elementId): void
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $this->client->post('/session/' . $this->sessionId . '/element/' . $elementId->toString() . '/click', []);
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#get-current-url
     *
     * @return string current URL
     */
    public function getCurrentUrl(): string
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $response = $this->client->get('/session/' . $this->sessionId . '/url');
        if (!array_key_exists('value', $response) ||
            !is_string($response['value']))  {
            throw new InvalidResponseException('response is invalid: ' . var_export($response, true));
        }

        return $response['value'];
    }
}
