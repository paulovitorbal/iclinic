<?php

declare(strict_types=1);

namespace App\Service\External;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class ExternalConsumer
{
    private Client $client;

    public function __construct(
        string $url,
        int $timeout = 2,
        private int $retry = 5,
        private ?LoggerInterface $logger = null,
        ?MockHandler $mockHandler = null
    ) {
        $params = [
            'base_uri' => $url,
            'timeout' => $timeout,
            'http_erros' => true,
        ];
        if ($mockHandler) {
            $params['handler'] = $mockHandler;
        }
        $this->client = new Client($params);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     * @throws TooMuchAttemptsException
     */
    public function get(string $path, string $token = ''): \stdClass
    {
        $lastException = null;
        for ($try = 0; $try <= $this->retry; $try++) {
            try {
                if ($token === '') {
                    $response = $this->client->get(
                        $path,
                        [
                        ]
                    );
                } else {
                    $response = $this->client->get(
                        $path,
                        [
                            'headers' => [
                                'Authorization' => $token
                            ]
                        ]
                    );
                }

                /** @var mixed $json */
                $json = json_decode(
                    $response->getBody()->getContents(),
                    false,
                    512,
                    JSON_THROW_ON_ERROR
                );

                Assert::isInstanceOf($json, \stdClass::class);
                return $json;
            } catch (ServerException $e) {
                if ($this->logger) {
                    $this->logger->error(
                        sprintf(
                            'Attempt #%d: %s',
                            $try,
                            var_export($e, true)
                        )
                    );
                }
                $lastException = $e;
            }
        }
        throw TooMuchAttemptsException::withPathAttempts(
            $path,
            $try,
            $lastException
        );
    }

    public function post(string $path, string $json, string $token = ''):\stdClass
    {
        $lastException = null;
        for ($try = 0; $try <= $this->retry; $try++) {
            try {
                if ($token === '') {
                    $response = $this->client->post(
                        $path,
                        [
                            'json' => $json
                        ]
                    );
                } else {
                    $response = $this->client->post(
                        $path,
                        [
                            'headers' => [
                                'Authorization' => $token
                            ],
                            'json' => $json
                        ]
                    );
                }

                /** @var mixed $json */
                $json = json_decode(
                    $response->getBody()->getContents(),
                    false,
                    512,
                    JSON_THROW_ON_ERROR
                );

                Assert::isInstanceOf($json, \stdClass::class);
                return $json;
            } catch (ServerException $e) {
                if ($this->logger) {
                    $this->logger->error(
                        sprintf(
                            'Attempt #%d: %s',
                            $try,
                            var_export($e, true)
                        )
                    );
                }
                $lastException = $e;
            }
        }
        throw TooMuchAttemptsException::withPathAttempts(
            $path,
            $try,
            $lastException
        );
    }
}
