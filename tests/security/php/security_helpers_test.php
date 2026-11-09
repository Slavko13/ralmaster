<?php

declare(strict_types=1);

$root = dirname(__DIR__, 3);

require $root . '/local/php_interface/include/ralmaster_security.php';

if (!function_exists('AddEventHandler')) {
    function AddEventHandler(...$args): void
    {
        // The validation class registers a Bitrix event when loaded in the app.
        // The CLI regression test only exercises its pure file checks.
    }
}

require $root . '/local/php_interface/include/ralmaster_estimate_form.php';

function assertSameValue($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL: {$message}\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

function assertNullValue($actual, string $message): void
{
    assertSameValue(null, $actual, $message);
}

$redirectCases = [
    ['/product/?page=2', '/product/?page=2'],
    ['//example.invalid/path', '/'],
    ['/%2fexample.invalid/path', '/'],
    ['/%252fexample.invalid/path', '/'],
    ['https://example.invalid/path', '/'],
    ["/safe\r\nLocation: https://example.invalid", '/'],
    ['/\\example.invalid', '/'],
];
foreach ($redirectCases as [$input, $expected]) {
    assertSameValue($expected, ralmaster_safe_local_path($input), "redirect sanitization for {$input}");
}

assertSameValue('shop.example.test', ralmaster_safe_configured_host('SHOP.example.test'), 'configured host normalization');
assertSameValue('', ralmaster_safe_configured_host('example.test/path'), 'configured host rejects paths');
assertSameValue('', ralmaster_safe_configured_host('127.0.0.1'), 'configured host rejects unqualified/private literals');

$validateContent = new ReflectionMethod(RalMasterEstimateFormValidator::class, 'validateFileContent');
$validateContent->setAccessible(true);
$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ralmaster-security-' . bin2hex(random_bytes(6));
if (!mkdir($tempDir, 0700) && !is_dir($tempDir)) {
    throw new RuntimeException('Unable to create security test directory');
}

try {
    $validPdf = $tempDir . DIRECTORY_SEPARATOR . 'valid.pdf';
    $invalidPdf = $tempDir . DIRECTORY_SEPARATOR . 'invalid.pdf';
    file_put_contents($validPdf, "%PDF-1.7\n%%EOF\n");
    file_put_contents($invalidPdf, "not a pdf\n");
    assertNullValue($validateContent->invoke(null, $validPdf, 'pdf'), 'valid PDF signature accepted');
    assertSameValue(
        'Содержимое файла не соответствует заявленному формату.',
        $validateContent->invoke(null, $invalidPdf, 'pdf'),
        'extension-only PDF spoof rejected'
    );

    $validDocx = $tempDir . DIRECTORY_SEPARATOR . 'valid.docx';
    $archive = new ZipArchive();
    if ($archive->open($validDocx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Unable to create Office regression fixture');
    }
    $archive->addFromString('[Content_Types].xml', '<Types/>');
    $archive->addFromString('word/document.xml', '<document/>');
    $archive->close();
    assertNullValue($validateContent->invoke(null, $validDocx, 'docx'), 'minimal DOCX structure accepted');

    $wrongOffice = $tempDir . DIRECTORY_SEPARATOR . 'wrong.docx';
    $archive = new ZipArchive();
    $archive->open($wrongOffice, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $archive->addFromString('[Content_Types].xml', '<Types/>');
    $archive->addFromString('xl/workbook.xml', '<workbook/>');
    $archive->close();
    assertSameValue(
        'Содержимое файла не соответствует заявленному формату.',
        $validateContent->invoke(null, $wrongOffice, 'docx'),
        'XLSX renamed to DOCX rejected'
    );
} finally {
    foreach (glob($tempDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($tempDir);
}

fwrite(STDOUT, "Security helper regression tests passed.\n");

