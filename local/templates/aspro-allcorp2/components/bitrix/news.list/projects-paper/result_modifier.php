<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$rmProjectAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/projects/';

$rmPaperProjects = array(
	'zhk-simvol' => array(
		'NAME' => 'ЖК «Символ»',
		'DESCRIPTION' => 'Декоративные корзины для кондиционеров',
		'YEAR' => '2025',
		'TYPE' => 'По индивидуальному проекту',
	),
	'zhk-belyy-grad' => array(
		'NAME' => 'ЖК «Белый град»',
		'DESCRIPTION' => 'Декоративные корзины для кондиционеров',
		'YEAR' => '2025',
		'TYPE' => 'По индивидуальному проекту',
	),
	'zhk-simvol-2' => array(
		'NAME' => 'ЖК «Символ 2»',
		'DESCRIPTION' => 'Декоративные корзины для кондиционеров',
		'YEAR' => '2025',
		'TYPE' => 'По индивидуальному проекту',
	),
);

foreach ($arResult['ITEMS'] as &$rmProjectItem) {
	$rmCode = mb_strtolower((string)($rmProjectItem['CODE'] ?? ''));
	$rmName = mb_strtolower((string)($rmProjectItem['NAME'] ?? ''));
	$rmImageFile = '';

	if (strpos($rmCode, 'simvol-2') !== false || strpos($rmName, 'символ 2') !== false) {
		$rmImageFile = 'project-symbol-2.webp';
	} elseif (strpos($rmCode, 'bel') !== false || strpos($rmName, 'бел') !== false || strpos($rmName, 'град') !== false) {
		$rmImageFile = 'project-white-city.webp';
	} elseif (strpos($rmCode, 'simvol') !== false || strpos($rmName, 'символ') !== false) {
		$rmImageFile = 'project-symbol.webp';
	}

	$rmDynamicImage = '';
	foreach (array('PREVIEW_PICTURE', 'DETAIL_PICTURE') as $rmImageField) {
		if (!empty($rmProjectItem[$rmImageField]['SRC'])) {
			$rmDynamicImage = (string)$rmProjectItem[$rmImageField]['SRC'];
			break;
		}
		if (!empty($rmProjectItem[$rmImageField])) {
			$rmDynamicImage = (string)CFile::GetPath($rmProjectItem[$rmImageField]);
			if ($rmDynamicImage) {
				break;
			}
		}
	}

	$rmDescription = trim(strip_tags((string)($rmProjectItem['PREVIEW_TEXT'] ?? '')));
	if ($rmDescription === '' && !empty($rmProjectItem['PROPERTIES']['TASK_PROJECT']['VALUE'])) {
		$rmTaskValue = $rmProjectItem['PROPERTIES']['TASK_PROJECT']['~VALUE'] ?? $rmProjectItem['PROPERTIES']['TASK_PROJECT']['VALUE'];
		if (is_array($rmTaskValue) && isset($rmTaskValue['TEXT'])) {
			$rmTaskValue = $rmTaskValue['TEXT'];
		}
		if (is_array($rmTaskValue)) {
			$rmTaskValue = implode(' ', array_filter($rmTaskValue, 'is_string'));
		}
		$rmDescription = trim(strip_tags((string)$rmTaskValue));
	}

	$rmPaperProject = $rmPaperProjects[$rmCode] ?? array();

	$rmProjectItem['RM_PROJECT'] = array(
		'IMAGE' => $rmImageFile ? $rmProjectAssetRoot.$rmImageFile : $rmDynamicImage,
		'IMAGE_SMALL' => $rmImageFile ? $rmProjectAssetRoot.preg_replace('~\.webp$~', '-768.webp', $rmImageFile) : '',
		'NAME' => $rmPaperProject['NAME'] ?? (string)$rmProjectItem['NAME'],
		'DESCRIPTION' => $rmDescription ?: ($rmPaperProject['DESCRIPTION'] ?? 'Подробнее о выполненном проекте и изготовленных для него изделиях.'),
		'YEAR' => $rmPaperProject['YEAR'] ?? '',
		'TYPE' => $rmPaperProject['TYPE'] ?? '',
	);
}
unset($rmProjectItem);
