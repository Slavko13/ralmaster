<?php

/**
 * Server-side validation for the dedicated RALMASTER estimate request form.
 *
 * The handler is intentionally scoped by the Web Form SID. Existing Aspro
 * forms are not affected.
 */
final class RalMasterEstimateFormValidator
{
    private const FORM_SID = 'ralmaster_estimate_s1';
    private const MAX_FILE_SIZE = 20 * 1024 * 1024;
    private const ALLOWED_EXTENSIONS = [
        'pdf',
        'png',
        'jpg',
        'jpeg',
        'webp',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'dwg',
        'dxf',
    ];

    /**
     * @param int|string $webFormId
     * @param array<string, mixed> $fields
     * @param array<string, mixed> $values
     */
    public static function onBeforeResultAdd($webFormId, array &$fields, array &$values): void
    {
        if (!self::isTargetForm((int) $webFormId)) {
            return;
        }

        $inputs = self::getInputsBySid((int) $webFormId, $values);
        $contact = trim((string) ($inputs['CONTACT']['value'] ?? ''));
        $task = trim((string) ($inputs['TASK']['value'] ?? ''));
        $file = $inputs['FILE']['file'] ?? null;
        $errors = [];

        if (!self::isValidContact($contact)) {
            $errors[] = 'Укажите корректный телефон или адрес электронной почты.';
        }

        $hasFile = self::hasUploadedFile($file);
        if ($task === '' && !$hasFile) {
            $errors[] = 'Опишите задачу или прикрепите файл.';
        }

        if ($hasFile) {
            $fileError = self::validateFile($file);
            if ($fileError !== null) {
                $errors[] = $fileError;
            }
        }

        if ($errors !== []) {
            global $APPLICATION;
            $APPLICATION->ThrowException(implode("\n", $errors));
        }
    }

    private static function isTargetForm(int $webFormId): bool
    {
        static $cache = [];

        if ($webFormId <= 0) {
            return false;
        }

        if (!array_key_exists($webFormId, $cache)) {
            $form = CForm::GetByID($webFormId)->Fetch();
            $cache[$webFormId] = is_array($form) && ($form['SID'] ?? '') === self::FORM_SID;
        }

        return $cache[$webFormId];
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, array{value?: mixed, file?: mixed}>
     */
    private static function getInputsBySid(int $webFormId, array &$values): array
    {
        $inputs = [];
        $fields = CFormField::GetList($webFormId, 'N', 's_sort', 'asc', ['ACTIVE' => 'Y']);

        while ($field = $fields->Fetch()) {
            $sid = (string) ($field['SID'] ?? '');
            if ($sid === '') {
                continue;
            }

            $answers = CFormAnswer::GetList((int) $field['ID'], 's_sort', 'asc', ['ACTIVE' => 'Y']);
            if (!$answer = $answers->Fetch()) {
                continue;
            }

            $type = (string) ($answer['FIELD_TYPE'] ?? '');
            $inputName = 'form_' . $type . '_' . (int) $answer['ID'];

            if ($type === 'file') {
                $inputs[$sid]['file'] = $_FILES[$inputName] ?? ($values[$inputName] ?? null);
                continue;
            }

            $inputs[$sid]['value'] = $values[$inputName] ?? ($_POST[$inputName] ?? '');
        }

        return $inputs;
    }

    private static function isValidContact(string $contact): bool
    {
        if ($contact === '') {
            return false;
        }

        if (filter_var($contact, FILTER_VALIDATE_EMAIL) !== false) {
            return true;
        }

        if (!preg_match('/^\+?[0-9\s().-]+$/u', $contact)) {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $contact);

        return is_string($digits) && strlen($digits) >= 10 && strlen($digits) <= 15;
    }

    /** @param mixed $file */
    private static function hasUploadedFile($file): bool
    {
        return is_array($file)
            && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            && (string) ($file['name'] ?? '') !== '';
    }

    /**
     * @param mixed $file
     */
    private static function validateFile($file): ?string
    {
        if (!is_array($file) || is_array($file['name'] ?? null)) {
            return 'Прикрепите один файл за одну отправку формы.';
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                return 'Размер файла превышает допустимый лимит 20 МБ.';
            }

            return 'Не удалось загрузить файл. Попробуйте ещё раз.';
        }

        if ((int) ($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return 'Размер файла превышает допустимый лимит 20 МБ.';
        }

        $fileName = basename(str_replace("\0", '', (string) ($file['name'] ?? '')));
        $extension = mb_strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return 'Недопустимый формат файла. Разрешены PDF, изображения, Office, DWG и DXF.';
        }

        return null;
    }
}

AddEventHandler(
    'form',
    'onBeforeResultAdd',
    [RalMasterEstimateFormValidator::class, 'onBeforeResultAdd']
);
