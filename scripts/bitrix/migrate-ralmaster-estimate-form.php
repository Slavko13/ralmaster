<?php

declare(strict_types=1);

const TARGET_FORM_SID = 'ralmaster_estimate_s1';
const SOURCE_FORM_SID = 'aspro_allcorp2_order_services_s1';

$arguments = array_slice($argv, 1);
$allowedArguments = ['--apply', '--dry-run', '--verify', '--help'];
$unknownArguments = array_values(array_diff($arguments, $allowedArguments));

if ($unknownArguments !== []) {
    fwrite(STDERR, 'Unknown argument(s): ' . implode(', ', $unknownArguments) . PHP_EOL);
    exit(2);
}
if (in_array('--help', $arguments, true)) {
    echo <<<'HELP'
Usage:
  php scripts/bitrix/migrate-ralmaster-estimate-form.php [--dry-run]
  php scripts/bitrix/migrate-ralmaster-estimate-form.php --verify
  php scripts/bitrix/migrate-ralmaster-estimate-form.php --apply

The default mode is --dry-run. Only --apply can write to the local database.
The migration never updates or deletes an existing Web Form.

HELP;
    exit(0);
}

if (in_array('--apply', $arguments, true) && in_array('--verify', $arguments, true)) {
    fwrite(STDERR, "Choose either --apply or --verify.\n");
    exit(2);
}

$mode = in_array('--apply', $arguments, true)
    ? 'apply'
    : (in_array('--verify', $arguments, true) ? 'verify' : 'dry-run');

$documentRoot = dirname(__DIR__, 2);
$_SERVER['DOCUMENT_ROOT'] = $documentRoot;
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_CRONTAB', true);

require $documentRoot . '/bitrix/modules/main/include/prolog_before.php';

if (!CModule::IncludeModule('form')) {
    fwrite(STDERR, "The Bitrix Web Forms module is unavailable.\n");
    exit(1);
}

/** @return array<string, mixed>|null */
function findFormBySid(string $sid): ?array
{
    $result = CForm::GetBySID($sid);
    $form = $result ? $result->Fetch() : false;

    return is_array($form) ? $form : null;
}

/** @return array<int, int> */
function getSourceGroupPermissions(int $sourceFormId): array
{
    $permissions = [];
    $filtered = false;
    $groups = CGroup::GetList('dropdown', 'asc', ['ADMIN' => 'N'], $filtered);

    while ($group = $groups->Fetch()) {
        $groupId = (int) $group['ID'];
        $permission = (int) CForm::GetPermission($sourceFormId, [$groupId], 'Y');
        if ($permission > 0) {
            $permissions[$groupId] = $permission;
        }
    }

    return $permissions;
}

/** @return array<string, array<string, mixed>> */
function expectedQuestions(): array
{
    return [
        'NAME' => [
            'title' => 'Ваше имя',
            'sort' => 100,
            'required' => 'N',
            'type' => 'text',
            'width' => 60,
            'height' => 0,
            'parameter' => 'autocomplete="name"',
        ],
        'CONTACT' => [
            'title' => 'Телефон или электронная почта',
            'sort' => 200,
            'required' => 'Y',
            'type' => 'text',
            'width' => 60,
            'height' => 0,
            'parameter' => '',
        ],
        'TASK' => [
            'title' => 'Описание задачи',
            'sort' => 300,
            'required' => 'N',
            'type' => 'textarea',
            'width' => 60,
            'height' => 6,
            'parameter' => '',
        ],
        'FILE' => [
            'title' => 'Чертежи, техническое задание или фотографии',
            'sort' => 400,
            'required' => 'N',
            'type' => 'file',
            'width' => 60,
            'height' => 0,
            'parameter' => 'accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx,.dwg,.dxf"',
        ],
        'CONTEXT' => [
            'title' => 'Источник запроса',
            'sort' => 500,
            'required' => 'N',
            'type' => 'hidden',
            'width' => 0,
            'height' => 0,
            'parameter' => '',
        ],
    ];
}

/** @param array<string, mixed> $source */
function formDefinition(array $source, array $mailTemplateIds = []): array
{
    return [
        'NAME' => 'РАЛМАСТЕР — запрос на расчёт',
        'SID' => TARGET_FORM_SID,
        'C_SORT' => (int) ($source['C_SORT'] ?? 100) + 10,
        'BUTTON' => 'Отправить запрос на расчёт',
        'USE_CAPTCHA' => (string) ($source['USE_CAPTCHA'] ?? 'N'),
        'DESCRIPTION' => 'Форма запроса ручного расчёта по макетам Paper.',
        'DESCRIPTION_TYPE' => 'text',
        'USE_DEFAULT_TEMPLATE' => 'Y',
        'arSITE' => CForm::GetSiteArray((int) $source['ID']),
        'arGROUP' => getSourceGroupPermissions((int) $source['ID']),
        'arMAIL_TEMPLATE' => $mailTemplateIds,
        'arIMAGE' => [],
    ];
}

/** @param array<string, mixed> $definition */
function createQuestion(int $formId, string $sid, array $definition): int
{
    $fieldId = CFormField::Set([
        'FORM_ID' => $formId,
        'ACTIVE' => 'Y',
        'TITLE' => $definition['title'],
        'TITLE_TYPE' => 'text',
        'SID' => $sid,
        'C_SORT' => $definition['sort'],
        'ADDITIONAL' => 'N',
        'REQUIRED' => $definition['required'],
        'IN_RESULTS_TABLE' => 'Y',
        'IN_EXCEL_TABLE' => 'Y',
        'FIELD_TYPE' => 'text',
        'COMMENTS' => '',
        'FILTER_TITLE' => $definition['title'],
        'RESULTS_TABLE_TITLE' => $definition['title'],
        'arANSWER' => [[
            'ID' => 0,
            'MESSAGE' => $definition['title'],
            'VALUE' => '',
            'C_SORT' => 100,
            'ACTIVE' => 'Y',
            'FIELD_TYPE' => $definition['type'],
            'FIELD_WIDTH' => $definition['width'],
            'FIELD_HEIGHT' => $definition['height'],
            'FIELD_PARAM' => $definition['parameter'],
        ]],
    ], false, 'N');

    if ((int) $fieldId <= 0) {
        throw new RuntimeException('Unable to create question ' . $sid . '.');
    }

    return (int) $fieldId;
}

function copyStatuses(int $sourceFormId, int $targetFormId): void
{
    $sourceStatuses = CFormStatus::GetList($sourceFormId, 's_sort', 'asc');
    $created = 0;

    while ($status = $sourceStatuses->Fetch()) {
        $view = $move = $edit = $delete = [];
        CFormStatus::GetPermissionList((int) $status['ID'], $view, $move, $edit, $delete);

        $statusId = CFormStatus::Set([
            'FORM_ID' => $targetFormId,
            'C_SORT' => (int) $status['C_SORT'],
            'ACTIVE' => (string) $status['ACTIVE'],
            'TITLE' => (string) $status['TITLE'],
            'DESCRIPTION' => (string) $status['DESCRIPTION'],
            'CSS' => (string) $status['CSS'],
            'HANDLER_OUT' => '',
            'HANDLER_IN' => '',
            'DEFAULT_VALUE' => (string) $status['DEFAULT_VALUE'],
            'arPERMISSION_VIEW' => $view,
            'arPERMISSION_MOVE' => $move,
            'arPERMISSION_EDIT' => $edit,
            'arPERMISSION_DELETE' => $delete,
        ], false, 'N');

        if ((int) $statusId <= 0) {
            throw new RuntimeException('Unable to create a Web Form status.');
        }

        ++$created;
    }

    if ($created === 0) {
        throw new RuntimeException('The source form has no statuses to copy safely.');
    }
}

/** @return list<string> */
function verifyForm(array $form): array
{
    $errors = [];
    $expected = expectedQuestions();
    $actual = [];
    $fields = CFormField::GetList((int) $form['ID'], 'N', 's_sort', 'asc');

    while ($field = $fields->Fetch()) {
        $sid = (string) $field['SID'];
        $answers = CFormAnswer::GetList((int) $field['ID'], 's_sort', 'asc');
        $answerRows = [];
        while ($answer = $answers->Fetch()) {
            $answerRows[] = $answer;
        }

        $actual[$sid] = ['field' => $field, 'answers' => $answerRows];
    }

    foreach ($expected as $sid => $definition) {
        if (!isset($actual[$sid])) {
            $errors[] = 'Missing question: ' . $sid;
            continue;
        }

        $field = $actual[$sid]['field'];
        $answers = $actual[$sid]['answers'];
        if ((string) $field['REQUIRED'] !== $definition['required']) {
            $errors[] = $sid . ': unexpected REQUIRED flag.';
        }
        if (count($answers) !== 1 || (string) ($answers[0]['FIELD_TYPE'] ?? '') !== $definition['type']) {
            $errors[] = $sid . ': unexpected answer type or answer count.';
        }
    }

    $extraSids = array_values(array_diff(array_keys($actual), array_keys($expected)));
    if ($extraSids !== []) {
        $errors[] = 'Unexpected questions: ' . implode(', ', $extraSids);
    }

    if ((int) CFormStatus::GetDefault((int) $form['ID']) <= 0) {
        $errors[] = 'The form has no active default status.';
    }

    if (empty(CForm::GetMailTemplateArray((int) $form['ID']))) {
        $errors[] = 'The form has no administrative mail template.';
    }

    return $errors;
}

$sourceForm = findFormBySid(SOURCE_FORM_SID);
if ($sourceForm === null) {
    fwrite(STDERR, 'Source form not found by SID: ' . SOURCE_FORM_SID . PHP_EOL);
    exit(1);
}

$targetForm = findFormBySid(TARGET_FORM_SID);

if ($mode === 'dry-run') {
    echo "DRY RUN — no database writes will be performed.\n";
    echo 'Source form found: ' . SOURCE_FORM_SID . PHP_EOL;

    if ($targetForm === null) {
        echo 'Target form is absent and would be created: ' . TARGET_FORM_SID . PHP_EOL;
        foreach (expectedQuestions() as $sid => $definition) {
            echo sprintf(
                "  - %s: %s, required=%s\n",
                $sid,
                $definition['type'],
                $definition['required']
            );
        }
    } else {
        $errors = verifyForm($targetForm);
        echo $errors === []
            ? "Target form already matches the expected schema; --apply would be a no-op.\n"
            : "Target form exists but differs from the expected schema; --apply would stop without changes.\n";
        foreach ($errors as $error) {
            echo '  - ' . $error . PHP_EOL;
        }
    }

    exit(0);
}

if ($mode === 'verify') {
    if ($targetForm === null) {
        fwrite(STDERR, 'Target form is not installed: ' . TARGET_FORM_SID . PHP_EOL);
        exit(1);
    }

    $errors = verifyForm($targetForm);
    if ($errors !== []) {
        fwrite(STDERR, "Target form verification failed:\n  - " . implode("\n  - ", $errors) . PHP_EOL);
        exit(1);
    }

    echo 'Target form schema verified: ' . TARGET_FORM_SID . PHP_EOL;
    exit(0);
}

if ($targetForm !== null) {
    $errors = verifyForm($targetForm);
    if ($errors !== []) {
        fwrite(STDERR, "Target form already exists and was not modified because its schema differs:\n  - " . implode("\n  - ", $errors) . PHP_EOL);
        exit(1);
    }

    echo 'Target form already matches the expected schema; nothing changed.' . PHP_EOL;
    exit(0);
}

global $DB, $strError;
$DB->StartTransaction();

try {
    $newFormId = (int) CForm::Set(formDefinition($sourceForm), false, 'N');
    if ($newFormId <= 0) {
        throw new RuntimeException('Unable to create the target Web Form. ' . trim((string) $strError));
    }

    foreach (expectedQuestions() as $sid => $definition) {
        createQuestion($newFormId, $sid, $definition);
    }

    copyStatuses((int) $sourceForm['ID'], $newFormId);

    $mailTemplateIds = array_map(
        'intval',
        (array) CForm::SetMailTemplate($newFormId, 'Y')
    );
    if ($mailTemplateIds === []) {
        throw new RuntimeException('Unable to create the administrative mail template.');
    }

    $updatedFormId = (int) CForm::Set(
        formDefinition($sourceForm, $mailTemplateIds),
        $newFormId,
        'N'
    );
    if ($updatedFormId !== $newFormId) {
        throw new RuntimeException('Unable to attach the administrative mail template.');
    }

    $createdForm = findFormBySid(TARGET_FORM_SID);
    if ($createdForm === null) {
        throw new RuntimeException('The target form cannot be read after creation.');
    }

    $verificationErrors = verifyForm($createdForm);
    if ($verificationErrors !== []) {
        throw new RuntimeException(implode(' ', $verificationErrors));
    }

    $DB->Commit();
    echo 'Created and verified Web Form: ' . TARGET_FORM_SID . PHP_EOL;
} catch (Throwable $exception) {
    $DB->Rollback();
    fwrite(STDERR, 'Migration rolled back: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
