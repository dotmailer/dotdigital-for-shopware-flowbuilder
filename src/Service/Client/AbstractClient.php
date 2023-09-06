<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class AbstractClient
{
    public const SELECT_LIMIT = 1000;

    protected LoggerInterface $logger;

    private ClientInterface $client;

    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Call Post
     *
     * @param array<string, mixed> $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array<string, mixed>
     */
    protected function post(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_POST, $uri, $options);
    }

    /**
     * Call Get
     *
     * @param array<string, mixed> $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array<string, mixed>
     */
    protected function get(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_GET, $uri, $options);
    }

    /**
     * Call Delete
     *
     * @param array<string, mixed> $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array<string, mixed>
     */
    protected function delete(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_DELETE, $uri, $options);
    }

    /**
     * Make new guzzle async request
     *
     * @param array<string, mixed> $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        $body = '';
        $this->logger->debug(
            'Sending {method} request to {uri} with the following content: {content}',
            [
                'method' => mb_strtoupper($method),
                'uri' => $uri,
                'content' => $options,
            ]
        );

        try {
            $response = $this->client->request($method, $uri, $options);
            $body = $response->getBody()->getContents();
            $this->logger->debug(
                'Received {code} from {method} {uri} with following response: {response}',
                [
                    'method' => mb_strtoupper($method),
                    'code' => sprintf('%s %s', $response->getStatusCode(), $response->getReasonPhrase()),
                    'uri' => $uri,
                    'headers' => $response->getHeaders(),
                    'response' => $body,
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital client request error',
                ['exception' => $exception]
            );
        }

        return json_decode($body, true) ?? [];
    }
}
