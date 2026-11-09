<?php

/**
 * Small, dependency-free security helpers used by public entry points.
 *
 * Keep this file independent from Bitrix so it can be regression-tested with
 * the PHP CLI before the framework is bootstrapped.
 */

/**
 * Return a same-origin absolute path or the supplied safe fallback.
 */
function ralmaster_safe_local_path($value, string $fallback = '/'): string
{
    if (!is_string($value) || $value === '' || strlen($value) > 2048) {
        return $fallback;
    }

    $value = trim($value);
    $decoded = $value;
    for ($i = 0; $i < 2; $i++) {
        $next = rawurldecode($decoded);
        if ($next === $decoded) {
            break;
        }
        $decoded = $next;
    }

    if (
        $value === ''
        || $value[0] !== '/'
        || str_starts_with($value, '//')
        || str_starts_with($decoded, '//')
        || str_contains($value, '\\')
        || str_contains($decoded, '\\')
        || preg_match('/[\x00-\x1F\x7F]/', $decoded) === 1
    ) {
        return $fallback;
    }

    $parts = parse_url($decoded);
    if (
        $parts === false
        || isset($parts['scheme'])
        || isset($parts['host'])
        || isset($parts['user'])
        || isset($parts['pass'])
    ) {
        return $fallback;
    }

    $path = (string) ($parts['path'] ?? '/');
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
        return $fallback;
    }

    $safe = $path;
    if (isset($parts['query']) && $parts['query'] !== '') {
        $safe .= '?' . $parts['query'];
    }

    return $safe;
}

/**
 * Validate a configured host name before it is used in a generated URL.
 */
function ralmaster_safe_configured_host($value): string
{
    if (!is_string($value)) {
        return '';
    }

    $host = mb_strtolower(trim($value));
    if ($host === '' || strlen($host) > 253) {
        return '';
    }

    $hostName = $host;
    if (preg_match('/^(.*):([0-9]{1,5})$/D', $host, $matches) === 1) {
        $hostName = $matches[1];
        $port = (int) $matches[2];
        if ($port < 1 || $port > 65535) {
            return '';
        }
    }

    if (filter_var($hostName, FILTER_VALIDATE_IP) !== false) {
        return '';
    }

    return preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?::[0-9]{1,5})?$/D', $host) === 1
        ? $host
        : '';
}

