<?php

declare(strict_types=1);

use App\DTO\Config;

return [
    'physicians' => [
        new Config(
            host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
            route: '/physicians/%d',
            authentication: env('PHYSICIANS_AUTHENTICATION_HEADER', ''),
            timeout: 4,
            retry: 2,
            cacheTtl: 48 * 60
        )
    ],
    'clinics' => [
        new Config(
            host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
            route: '/clinics/%d',
            authentication: env('CLINICS_AUTHENTICATION_HEADER', ''),
            timeout: 5,
            retry: 3,
            cacheTtl: 72 * 60
        )
    ],
    'patients' => [
        new Config(
            host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
            route: '/patients/%d',
            authentication: env('PATIENTS_AUTHENTICATION_HEADER', ''),
            timeout: 3,
            retry: 2,
            cacheTtl: 12 * 60
        )
    ],
    'metrics' => [
        new Config(
            host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
            route: '/metrics',
            authentication: env('METRICS_AUTHENTICATION_HEADER', ''),
            timeout: 6,
            retry: 5,
            cacheTtl: null
        )
    ],
];
