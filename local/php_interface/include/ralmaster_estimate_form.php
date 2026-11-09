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
    private const MAX_IMAGE_PIXELS = 40_000_000;
    private const MAX_IMAGE_DIMENSION = 12_000;
    private const MAX_ARCHIVE_ENTRIES = 1_000;
    private const MAX_ARCHIVE_UNCOMPRESSED_SIZE = 100 * 1024 * 1024;
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
        $context = trim((string) ($inputs['CONTEXT']['value'] ?? ''));
        $task = trim((string) ($inputs['TASK']['value'] ?? ''));
        $file = $inputs['FILE']['file'] ?? null;
        $errors = [];

        if (mb_strlen($contact) > 320 || preg_match('/[\r\n]/u', $contact) === 1) {
            $errors[] = 'Контактные данные содержат недопустимые символы или слишком длинные.';
        }
        if (mb_strlen($context) > 2000) {
            $errors[] = 'Служебные данные формы превышают допустимый размер.';
        }
        if (mb_strlen($task) > 10000) {
            $errors[] = 'Описание задачи превышает допустимый размер.';
        }

        $contactError = self::getContactError($contact, self::getContactMethod($context));
        if ($contactError !== null) {
            $errors[] = $contactError;
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

        return self::isValidPhone($contact);
    }

    private static function getContactMethod(string $context): string
    {
        if (preg_match('/(?:^|;\s*)contact=(telegram|max|phone|email)(?:;|$)/iu', $context, $matches) !== 1) {
            return '';
        }

        return mb_strtolower((string) $matches[1]);
    }

    private static function getContactError(string $contact, string $method): ?string
    {
        if ($method === 'email') {
            return filter_var($contact, FILTER_VALIDATE_EMAIL) !== false
                ? null
                : 'Укажите корректный адрес электронной почты.';
        }

        if ($method === 'phone') {
            return self::isValidPhone($contact)
                ? null
                : 'Укажите корректный номер телефона.';
        }

        if ($method === 'telegram' || $method === 'max') {
            return mb_strlen($contact) >= 3 && preg_match('/[\r\n]/u', $contact) !== 1
                ? null
                : 'Укажите имя пользователя или номер телефона.';
        }

        return self::isValidContact($contact)
            ? null
            : 'Укажите корректный телефон или адрес электронной почты.';
    }

    private static function isValidPhone(string $contact): bool
    {
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

        if ((int) ($file['size'] ?? 0) <= 0) {
            return 'Загруженный файл пуст.';
        }

        $rawFileName = str_replace("\0", '', (string) ($file['name'] ?? ''));
        $fileName = basename(str_replace('\\', '/', $rawFileName));
        if (
            $fileName === ''
            || $fileName !== $rawFileName
            || mb_strlen($fileName) > 180
            || preg_match('/[\x00-\x1F\x7F]/u', $fileName) === 1
        ) {
            return 'Имя файла содержит недопустимые символы.';
        }

        $extension = mb_strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return 'Недопустимый формат файла. Разрешены PDF, изображения, Office, DWG и DXF.';
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_file($tmpName) || !is_readable($tmpName)) {
            return 'Не удалось проверить загруженный файл.';
        }
        if (PHP_SAPI !== 'cli' && !is_uploaded_file($tmpName)) {
            return 'Источник загруженного файла не подтверждён.';
        }

        $contentError = self::validateFileContent($tmpName, $extension);
        if ($contentError !== null) {
            return $contentError;
        }

        return null;
    }

    private static function validateFileContent(string $path, string $extension): ?string
    {
        $prefix = file_get_contents($path, false, null, 0, 8192);
        if (!is_string($prefix)) {
            return 'Не удалось проверить содержимое файла.';
        }

        if ($extension === 'pdf') {
            return str_starts_with($prefix, '%PDF-') ? null : self::contentMismatchError();
        }

        if (in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            $expectedTypes = [
                'png' => IMAGETYPE_PNG,
                'jpg' => IMAGETYPE_JPEG,
                'jpeg' => IMAGETYPE_JPEG,
                'webp' => IMAGETYPE_WEBP,
            ];
            $type = @exif_imagetype($path);
            $dimensions = @getimagesize($path);
            if ($type !== $expectedTypes[$extension] || !is_array($dimensions)) {
                return self::contentMismatchError();
            }

            $width = (int) ($dimensions[0] ?? 0);
            $height = (int) ($dimensions[1] ?? 0);
            if (
                $width <= 0
                || $height <= 0
                || $width > self::MAX_IMAGE_DIMENSION
                || $height > self::MAX_IMAGE_DIMENSION
                || ($width * $height) > self::MAX_IMAGE_PIXELS
            ) {
                return 'Размеры изображения превышают безопасный лимит.';
            }

            return null;
        }

        if (in_array($extension, ['doc', 'xls'], true)) {
            return str_starts_with($prefix, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")
                ? null
                : self::contentMismatchError();
        }

        if (in_array($extension, ['docx', 'xlsx'], true)) {
            return self::validateOfficeArchive($path, $extension);
        }

        if ($extension === 'dwg') {
            return preg_match('/^AC10[0-9]{2}/D', $prefix) === 1 ? null : self::contentMismatchError();
        }

        if ($extension === 'dxf') {
            $isBinary = str_starts_with($prefix, "AutoCAD Binary DXF\r\n\x1A\0");
            $isAscii = preg_match('/(?:^|\R)\s*0\s*\R\s*SECTION\s*(?:\R|$)/i', $prefix) === 1;
            return ($isBinary || $isAscii) ? null : self::contentMismatchError();
        }

        return self::contentMismatchError();
    }

    private static function validateOfficeArchive(string $path, string $extension): ?string
    {
        if (!class_exists(ZipArchive::class)) {
            return 'Проверка Office-файлов временно недоступна.';
        }

        $archive = new ZipArchive();
        if ($archive->open($path) !== true) {
            return self::contentMismatchError();
        }

        try {
            if ($archive->numFiles <= 0 || $archive->numFiles > self::MAX_ARCHIVE_ENTRIES) {
                return 'Архив Office-файла содержит недопустимое количество элементов.';
            }

            $totalSize = 0;
            $hasContentTypes = false;
            $hasExpectedFolder = false;
            $expectedPrefix = $extension === 'docx' ? 'word/' : 'xl/';

            for ($index = 0; $index < $archive->numFiles; $index++) {
                $stat = $archive->statIndex($index);
                if (!is_array($stat)) {
                    return self::contentMismatchError();
                }

                $name = str_replace('\\', '/', (string) ($stat['name'] ?? ''));
                if ($name === '' || str_starts_with($name, '/') || preg_match('~(?:^|/)\.\.(?:/|$)~', $name) === 1) {
                    return 'Office-файл содержит небезопасный путь внутри архива.';
                }

                $totalSize += max(0, (int) ($stat['size'] ?? 0));
                if ($totalSize > self::MAX_ARCHIVE_UNCOMPRESSED_SIZE) {
                    return 'Распакованный размер Office-файла превышает безопасный лимит.';
                }

                $hasContentTypes = $hasContentTypes || $name === '[Content_Types].xml';
                $hasExpectedFolder = $hasExpectedFolder || str_starts_with($name, $expectedPrefix);
            }

            return ($hasContentTypes && $hasExpectedFolder) ? null : self::contentMismatchError();
        } finally {
            $archive->close();
        }
    }

    private static function contentMismatchError(): string
    {
        return 'Содержимое файла не соответствует заявленному формату.';
    }
}

AddEventHandler(
    'form',
    'onBeforeResultAdd',
    [RalMasterEstimateFormValidator::class, 'onBeforeResultAdd']
);
