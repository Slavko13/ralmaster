<?php

$env = static function (string $name, string $default = ''): string {
    $value = getenv($name);

    return $value === false || $value === '' ? $default : $value;
};

return [
    'connections' => [
        'value' => [
            'default' => [
                'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
                'host' => $env('BITRIX_DB_HOST', 'db'),
                'port' => (int) $env('BITRIX_DB_PORT', '3306'),
                'database' => $env('BITRIX_DB_NAME', 'bitrix'),
                'login' => $env('BITRIX_DB_USER', 'bitrix'),
                'password' => $env('BITRIX_DB_PASSWORD', 'bitrix_local_only'),
                'options' => 2,
            ],
        ],
        'readonly' => true,
    ],
];
