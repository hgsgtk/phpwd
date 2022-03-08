<?php

declare(strict_types=1);

namespace Phpwd;

use GuzzleHttp\Exception\ClientException;
use Phpwd\Exceptions\BadMethodCallException;
use Phpwd\Exceptions\HttpException;
use Phpwd\Exceptions\InvalidArgumentException;
use Phpwd\Exceptions\InvalidResponseException;
use Phpwd\Exceptions\LogicException;

/**
 * It supports only chromedriver.
 */
final class Webdriver
{
    /**
     * @var string WebDriver remote end url
     * @link https://w3c.github.io/webdriver/#nodes
     */
    private string $remoteEndUrl;

    /**
     * @var string|null sessionId
     * @link https://www.w3.org/TR/webdriver/#new-session
     */
    private string|null $sessionId = null;

    /**
     * @param string|null $remoteEndUrl
     */
    public function __construct(?string $remoteEndUrl = null)
    {
        $this->remoteEndUrl = $remoteEndUrl ?? 'http://localhost:9515';
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
        $response = $this->sendPost('/session', [
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

        $this->sendDelete('/session/' . $this->sessionId);
    }

    public function navigateTo(string $url): void
    {
        if (!$this->sessionId) {
            throw new LogicException('you need to start a session by openBrowser()');
        }

        $this->sendPost('/session/' . $this->sessionId . '/url', [
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

        $response = $this->sendPost('/session/' . $this->sessionId . '/element', [
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

        $response = $this->sendGet('/session/' . $this->sessionId . '/element/' . $elementId->toString() . '/text');

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

        $this->sendPost('/session/' . $this->sessionId . '/element/' . $elementId->toString() . '/click', []);
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

        $response = $this->sendGet('/session/' . $this->sessionId . '/url');
        if (!array_key_exists('value', $response) ||
            !is_string($response['value']))  {
            throw new InvalidResponseException('response is invalid: ' . var_export($response, true));
        }

        return $response['value'];
    }

    /**
     * send GET request.
     *
     * @todo extract duplicate codes about curl handling.
     * @todo move another class which is responsive for http client
     *
     * @param string $path
     * @return array<string,mixed>
     */
    private function sendGet(string $path): array
    {
        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->remoteEndUrl,
                'timeout' => 5.0,
            ]);

            $response = $client->request('GET', $path, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (ClientException $e) {
            throw new HttpException("http client error", $e->getCode(), $e);
        }

        $decodedBody = json_decode((string)$response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
        }

        return $decodedBody;

    }

    /**
     * send POST request.
     *
     * @todo extract duplicate codes about curl handling.
     *
     * @param string $path
     * @param array $body
     * @return array<string,mixed>
     */
    private function sendPost(string $path, array $body): array
    {
        if ($body === []) {
            // When a request body is empty array, we should encode to JSON object, not array.
            // If we request empty array to webdriver remote end, we'll got the following error (e.g. click element)
            // >  '{"value":{"error":"invalid argument","message":"invalid argument: missing command parameters"...
            $encodedBody = json_encode($body, JSON_FORCE_OBJECT);
        } else {
            $encodedBody = json_encode($body);
        }
        if (!$encodedBody) {
            throw new InvalidArgumentException('invalid body: ' . var_export($body, true));
        }
        
        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->remoteEndUrl,
                'timeout' => 5.0,
            ]);

            $response = $client->request('POST', $this->remoteEndUrl . $path, [
                'headers' => [
                    'Content-Type' => 'application/json', 
                ],
                'body' => $encodedBody,
            ]);


        } catch (ClientException $e) {
            throw new HttpException("http client error", $e->getCode(), $e);
        }

        $decodedBody = json_decode((string)$response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
        }

        return $decodedBody;

    }

    /**
     * send DELETE request.
     *
     * @todo extract duplicate codes about curl handling.
     *
     * @return array<string,mixed>
     */
    private function sendDelete(string $path): array
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->remoteEndUrl . $path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            // For DELETE request
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            $response = curl_exec($ch);

            if (!is_string($response) || curl_errno($ch)) {
                throw new HttpException('cURL request error: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                throw new HttpException(
                    "response code is expected 200, but got status code '{$httpCode}' and response '{$response}'");
            }

            $decodedBody = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
            }

            return $decodedBody;
        } finally {
            curl_close($ch);
        }
    }
}
