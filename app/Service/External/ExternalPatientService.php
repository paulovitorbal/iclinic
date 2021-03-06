<?php

declare(strict_types=1);

namespace App\Service\External;

use App\DTO\Config;
use App\DTO\Patient;
use App\DTO\StdClassFactory;
use App\Exceptions\NotFound;
use App\Exceptions\TooMuchRetries;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class ExternalPatientService
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
    public function getPatient(int $id): Patient
    {
        if ($this->cache->has($this->getPatientRoute($id))) {
            /** @var Patient $patient */
            $patient = $this->cache->get($this->getPatientRoute($id));
            return $patient;
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
                $this->getPatientRoute($id),
                $this->getConfig()->getAuthentication()
            );
        } catch (TooMuchAttemptsException $e) {
            throw TooMuchRetries::patientNotAvailable($e);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw NotFound::patientNotFound($e);
            }
            throw $e;
        }

        $patient = $this->factory->createPatient($stdObject);

        $this->cache->set(
            $this->getPatientRoute($id),
            $patient,
            $this->getConfig()->getCacheAsDateInterval()
        );

        return $patient;
    }

    private function getPatientRoute(int $id): string
    {
        return sprintf(
            $this->getConfig()->getRoute(),
            $id
        );
    }

    private function getConfig(): Config
    {
        $config = config('external-services.patients');
        Assert::isInstanceOf($config, Config::class);
        return $config;
    }
}
