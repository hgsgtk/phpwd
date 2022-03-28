<?php

declare(strict_types=1);

namespace Phpwd;

use Phpwd\Exceptions\InvalidResponseException;

final class Element
{
    private ClientInterface $client;

    public function __construct(
        private SessionId $sessionId,
        private ElementId $elementId,
        ?string $remoteEndUrl = null,
    )
    {
        $remoteEndUrl = $remoteEndUrl ?? 'http://localhost:9515';

        $this->client = new HttpClient($remoteEndUrl);
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#dfn-get-element-text
     */
    public function getText(): string
    {
        $response = $this->client->get(
            '/session/' . $this->sessionId->toString() .
             '/element/' . $this->elementId->toString() . '/text');

        if (!array_key_exists('value', $response) ||
            !is_string($response['value']))  {
            throw new InvalidResponseException('response is invalid: ' . var_export($response, true));
        }

        return $response['value'];
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#element-click
     */
    public function click(): void
    {
        $this->client->post('/session/' . $this->sessionId->toString() .
         '/element/' . $this->elementId->toString() . '/click', []);
    }

    /**
     * @link https://www.w3.org/TR/webdriver/#element-send-keys
     */
    public function type(string $input): void
    {
        $this->client->post('/session/' . $this->sessionId->toString() .
        '/element/' . $this->elementId->toString() . '/value', [
            'text' => 'Autify',
        ]);
    }
}
