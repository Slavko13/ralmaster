<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/logs/addMessage2Log.log");

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Dump.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/ralmaster_security.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/ralmaster_estimate_form.php';
spl_autoload_register('autoLoad');

AddEventHandler('main', 'OnEndBufferContent', 'ralmasterRemoveExternalFontLinks');

function ralmasterRemoveExternalFontLinks(&$content)
{
    if (!is_string($content) || stripos($content, '<html') === false) {
        return;
    }

    $content = preg_replace(
        '~<link\\b(?=[^>]*https?://fonts\\.(?:googleapis|gstatic)\\.com)[^>]*>\\s*~i',
        '',
        $content
    );

    // The legacy Bitrix Yandex map component still emits an HTTP loader.
    // Keep the existing map and key, but prevent mixed content on HTTPS pages.
    $content = str_ireplace(
        'http://api-maps.yandex.ru',
        'https://api-maps.yandex.ru',
        $content
    );
}
