<?php

declare(strict_types=1);

namespace Phpwd;

use Phpwd\Exceptions\InvalidResponseException;

/**
 * It supports only chromedriver.
 */
final class Webdriver
{
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

    public function openBrowser(): Browser
    {
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

        return new Browser(new SessionId($response['value']['sessionId']));
    }
}
