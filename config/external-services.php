<?php

declare(strict_types=1);

return [
    'physicians' => [
        'host' => 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
        'route' => '/physicians/%d',
        'authentication' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4g' .
            'RG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJzZXJ2aWNlIjoicGh5c2ljaWFucyJ9.Ei58MtFFGBK4uzpxwnzLxG0Ljdd-NQKVcOXIS4UYJtA',
        'timeout' => 4,
        'retry' => 2,
        'cacheTtl' => 48 * 60
    ],
    'clinics' => [
        'host' => 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
        'route' => '/clinics/%d',
        'authentication' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4g' .
            'RG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJzZXJ2aWNlIjoiY2xpbmljcyJ9.r3w8KS4LfkKqZhOUK8YnIdLhVGJEqnReSClLCMBIJRQ',
        'timeout' => 5,
        'retry' => 3,
        'cacheTtl' => 72 * 60
    ],
    'patients' => [
        'host' => 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
        'route' => '/patients/%d',
        'authentication' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4g' .
            'RG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJzZXJ2aWNlIjoicGF0aWVudHMifQ.Pr6Z58GzNRtjX8Y09hEBzl7dluxsGiaxGlfzdaphzVU',
        'timeout' => 3,
        'retry' => 2,
        'cacheTtl' => 12 * 60
    ],
    'metrics' => [
        'host' => 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
        'route' => '/metrics',
        'authentication' => 'Bearer SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
        'timeout' => 6,
        'retry' => 5,
        'cacheTtl' => null
    ],
];
