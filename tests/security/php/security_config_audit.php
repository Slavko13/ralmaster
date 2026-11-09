<?php

declare(strict_types=1);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$keys = [
    'security' => [
        'filter_action',
        'filter_stop',
        'filter_log',
        'antivirus_action',
        'session',
        'otp_mandatory_using',
        'otp_allow_recovery_codes',
        'redirect_referer_check',
        'redirect_referer_site_check',
        'redirect_href_sign',
        'redirect_log',
    ],
    'main' => [
        'captcha_registration',
        'captcha_restoring_password',
        'use_session_id_ttl',
        'event_log_login_success',
        'event_log_login_fail',
        'event_log_password_change',
        'new_user_registration',
    ],
    'statistic' => [
        'DEFENCE_ON',
        'DEFENCE_LOG',
    ],
];

$result = [];
foreach ($keys as $module => $names) {
    foreach ($names as $name) {
        $result[$module][$name] = COption::GetOptionString($module, $name, '<unset>');
    }
}

$result['modules'] = [];
$moduleRows = CModule::GetList();
while ($module = $moduleRows->Fetch()) {
    if (in_array($module['ID'], ['main', 'security', 'aspro.allcorp2'], true)) {
        $moduleObject = CModule::CreateModuleObject($module['ID']);
        $result['modules'][$module['ID']] = [
            'version' => $moduleObject?->MODULE_VERSION,
            'version_date' => $moduleObject?->MODULE_VERSION_DATE,
        ];
    }
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
