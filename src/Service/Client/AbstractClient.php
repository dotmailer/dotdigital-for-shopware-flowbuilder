<?php

namespace Dotdigital\Flow\Service\Client;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class AbstractClient
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * AbstractClient constructor.
     *
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Call Post
     *
     * @param string $uri
     * @param array<string,mixed> $options
     *
     * @return array<string,mixed>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function post(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_POST, $uri, $options);
    }

    /**
     * Make new guzzle async request
     *
     * @param string $method
     * @param string $uri
     * @param array<string,mixed> $options
     *
     * @return array<string,mixed>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        $body = '';
        $this->logger->debug(
            'Sending {method} request to {uri} with the following content: {content}',
            [
                'method' => \mb_strtoupper($method),
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
                    'method' => \mb_strtoupper($method),
                    'code' => \sprintf('%s %s', $response->getStatusCode(), $response->getReasonPhrase()),
                    'uri' => $uri,
                    'headers' => $response->getHeaders(),
                    'response' => $body,
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return \json_decode($body, true) ?? [];
    }
}
