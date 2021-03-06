<?php

declare(strict_types=1);

namespace App\Service\External;

use App\DTO\Config;
use App\DTO\Physician;
use App\DTO\StdClassFactory;
use App\Exceptions\NotFound;
use App\Exceptions\TooMuchRetries;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class ExternalPhysicianService
{
    public function __construct(
        private Repository $cache,
        private LoggerInterface $logger,
        private StdClassFactory $factory,
        private ?MockHandler $mockHandler = null
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     * @throws NotFound
     */
    public function getPhysician(int $id): Physician
    {
        if ($this->cache->has($this->getPhysicianRoute($id))) {
            /** @var Physician $physician */
            $physician = $this->cache->get($this->getPhysicianRoute($id));
            return $physician;
        }

        $externalConsumer = new ExternalConsumer(
            $this->getConfig()->getHost(),
            $this->getConfig()->getTimeout(),
            $this->getConfig()->getRetry(),
            $this->logger,
            $this->mockHandler
        );
        try {
                $stdObject = $externalConsumer->get(
                    $this->getPhysicianRoute($id),
                    $this->getConfig()->getAuthentication()
                );
        } catch (TooMuchAttemptsException $e) {
            throw TooMuchRetries::physicianNotAvailable($e);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw NotFound::physicianNotFound($e);
            }
            throw $e;
        }

        $physician = $this->factory->createPhysician($stdObject);

        $this->cache->set(
            $this->getPhysicianRoute($id),
            $physician,
            $this->getConfig()->getCacheAsDateInterval()
        );

        return $physician;
    }

    private function getPhysicianRoute(int $id): string
    {
        return sprintf(
            $this->getConfig()->getRoute(),
            $id
        );
    }

    private function getConfig(): Config
    {
        $config = config('external-services.physicians');
        Assert::isInstanceOf($config, Config::class);
        return $config;
    }
}
