<?php 

declare(strict_types=1);

namespace Phpwd;

use GuzzleHttp\Exception\TransferException;
use Phpwd\Exceptions\HttpException;
use Phpwd\Exceptions\InvalidArgumentException;
use Phpwd\Exceptions\InvalidResponseException;

final class HttpClient implements ClientInterface
{   
    private const BASE_HTTP_HEADERS = [
        'Content-Type' => 'application/json', 
    ];

    private \GuzzleHttp\ClientInterface $client;

    /**
     * @param string|null $remoteEndUrl WebDriver remote end url
     * @link https://w3c.github.io/webdriver/#nodes
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        $this->client = new \GuzzleHttp\Client([
            'timeout' => 5.0,
        ]);
    }

    public function get(string $path): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . $path, [
                'headers' => self::BASE_HTTP_HEADERS,
            ]);
        } catch (TransferException $e) {
            throw new HttpException("http client error", $e->getCode(), $e);
        }

        $decodedBody = json_decode((string)$response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
        }

        return $decodedBody;
    }

    public function post(string $path, array $body): array
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
            $response = $this->client->request('POST', $this->baseUrl . $path, [
                'headers' => self::BASE_HTTP_HEADERS,
                'body' => $encodedBody,
            ]);
        } catch (TransferException $e) {
            throw new HttpException("http client error", $e->getCode(), $e);
        }

        $decodedBody = json_decode((string)$response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
        }

        return $decodedBody;
    }

    public function delete(string $path): array
    {
        try {
            $response = $this->client->request('DELETE', $this->baseUrl . $path, [
                'headers' => self::BASE_HTTP_HEADERS,
            ]);
        } catch (TransferException $e) {
            throw new HttpException("http client error", $e->getCode(), $e);
        }
        $decodedBody = json_decode((string)$response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("JSON decode error: " . json_last_error_msg());
        }

        return $decodedBody;
    }
}
