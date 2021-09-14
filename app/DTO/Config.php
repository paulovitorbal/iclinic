<?php

declare(strict_types=1);

namespace App\DTO;

class Config
{
    public function __construct(
        private string $host,
        private string $route,
        private string $authentication,
        private int    $timeout,
        private int    $retry,
        private ?int   $cacheTtl,
    )
    {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getAuthentication(): string
    {
        return $this->authentication;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getTimeoutAsDateInterval(): \DateInterval
    {
        return new \DateInterval(
            sprintf('PT%dS', $this->timeout)
        );
    }

    public function getRetry(): int
    {
        return $this->retry;
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl ?? 0;
    }
}
