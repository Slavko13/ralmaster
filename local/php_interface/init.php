<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/logs/addMessage2Log.log");

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Dump.php';
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
}
