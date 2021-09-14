<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Patient;
use App\DTO\StdClassFactory;
use App\Service\External\ExternalConsumer;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;

class ExternalService
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
    public function getPatient(int $id): Patient
    {
        if ($this->cache->has($this->getPatientRoute($id))) {
            /** @var Patient $patient */
            $patient = $this->cache->get($this->getPatientRoute($id));
            return $patient;
        }

        $externalConsumer = new ExternalConsumer(
            (string)config('external-services.patients.host'),
            (int)config('external-services.patients.timeout'),
            (int)config('external-services.patients.retry'),
            $this->logger,
            $this->mockHandler
        );

        $stdObject = $externalConsumer->get(
            $this->getPatientRoute($id),
            (string)config('external-services.patients.authentication')
        );

        $patient = $this->factory->createPatient($stdObject);

        $result = $this->cache->set(
            $this->getPatientRoute($id),
            $patient,
            $this->getExpirationFor(
                (int)config('external-services.patients.cacheTtl')
            )
        );

        if ($result === false) {
            throw new \RuntimeException('An error occured when setting the cache.');
        }

        return $patient;
    }

    private function getPatientRoute(int $id): string
    {
        return sprintf(
            (string)config('external-services.patients.route'),
            $id
        );
    }

    private function getExpirationFor(int $seconds): \DateInterval
    {
        return new \DateInterval(
            sprintf('PT%dS', $seconds)
        );
    }
}
