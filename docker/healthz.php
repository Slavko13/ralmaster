<?php

header('Content-Type: application/json; charset=utf-8');

$requiredExtensions = ['curl', 'gd', 'intl', 'mbstring', 'mysqli', 'zip'];
$missingExtensions = array_values(array_filter(
    $requiredExtensions,
    static fn (string $extension): bool => !extension_loaded($extension)
));

$result = [
    'status' => 'ok',
    'php' => PHP_VERSION,
    'missingExtensions' => $missingExtensions,
    'database' => false,
];

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $database = new mysqli(
        getenv('BITRIX_DB_HOST') ?: 'db',
        getenv('BITRIX_DB_USER') ?: 'bitrix',
        getenv('BITRIX_DB_PASSWORD') ?: 'bitrix_local_only',
        getenv('BITRIX_DB_NAME') ?: 'bitrix',
        (int) (getenv('BITRIX_DB_PORT') ?: 3306)
    );
    $database->query('SELECT 1');
    $database->close();
    $result['database'] = true;
} catch (Throwable $exception) {
    $result['status'] = 'error';
    $result['databaseError'] = get_class($exception);
}

if ($missingExtensions !== [] || $result['database'] !== true) {
    $result['status'] = 'error';
    http_response_code(503);
}

echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
