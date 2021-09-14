<?php

declare(strict_types=1);

namespace App\Service\External;

use App\DTO\Config;
use App\DTO\NewMetricResponse;
use App\DTO\NewMetricsRequest;
use App\DTO\StdClassFactory;
use GuzzleHttp\Handler\MockHandler;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class ExternalMetricService
{
    public function __construct(
        private LoggerInterface $logger,
        private StdClassFactory $factory,
        private ?MockHandler $mockHandler = null
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function post(NewMetricsRequest $request): NewMetricResponse
    {
        $externalConsumer = new ExternalConsumer(
            $this->getConfig()->getHost(),
            $this->getConfig()->getTimeout(),
            $this->getConfig()->getRetry(),
            $this->logger,
            $this->mockHandler
        );

        $stdObject = $externalConsumer->post(
            $this->getConfig()->getRoute(),
            json_encode($request, JSON_THROW_ON_ERROR),
            $this->getConfig()->getAuthentication()
        );

        return $this->factory->createMetricResponse($stdObject);
    }


    private function getConfig(): Config
    {
        $config = config('external-services.metrics');
        Assert::isInstanceOf($config, Config::class);
        return $config;
    }
}
