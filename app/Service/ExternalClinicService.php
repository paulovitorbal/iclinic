<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Clinic;
use App\DTO\Config;
use App\DTO\StdClassFactory;
use App\Service\External\ExternalConsumer;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class ExternalClinicService
{
    public function __construct(
        private Repository      $cache,
        private LoggerInterface $logger,
        private StdClassFactory $factory,
        private ?MockHandler    $mockHandler = null
    )
    {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getClinic(int $id): Clinic
    {
        if ($this->cache->has($this->getClinicRoute($id))) {
            /** @var Clinic $clinic */
            $clinic = $this->cache->get($this->getClinicRoute($id));
            return $clinic;
        }

        $externalConsumer = new ExternalConsumer(
            $this->getConfig()->getHost(),
            $this->getConfig()->getTimeout(),
            $this->getConfig()->getRetry(),
            $this->logger,
            $this->mockHandler
        );

        $stdObject = $externalConsumer->get(
            $this->getClinicRoute($id),
            $this->getConfig()->getAuthentication()
        );

        $clinic = $this->factory->createClinic($stdObject);

        $this->cache->set(
            $this->getClinicRoute($id),
            $clinic,
            $this->getConfig()->getTimeoutAsDateInterval()
        );

        return $clinic;
    }

    private function getClinicRoute(int $id): string
    {
        return sprintf(
            $this->getConfig()->getRoute(),
            $id
        );
    }

    private function getConfig(): Config
    {
        $config = config('external-services.clinics');
        Assert::isInstanceOf($config, Config::class);
        return $config;
    }
}
