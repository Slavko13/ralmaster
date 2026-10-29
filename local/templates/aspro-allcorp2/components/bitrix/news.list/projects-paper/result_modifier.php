<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$rmProjectAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/projects/';

foreach ($arResult['ITEMS'] as &$rmProjectItem) {
	$rmCode = mb_strtolower((string)($rmProjectItem['CODE'] ?? ''));
	$rmName = mb_strtolower((string)($rmProjectItem['NAME'] ?? ''));
	$rmImageFile = '';

	if (strpos($rmCode, 'simvol-2') !== false || strpos($rmName, 'символ 2') !== false) {
		$rmImageFile = 'project-symbol-2.png';
	} elseif (strpos($rmCode, 'bel') !== false || strpos($rmName, 'бел') !== false || strpos($rmName, 'град') !== false) {
		$rmImageFile = 'project-white-city.jpg';
	} elseif (strpos($rmCode, 'simvol') !== false || strpos($rmName, 'символ') !== false) {
		$rmImageFile = 'project-symbol.jpg';
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

	$rmProjectItem['RM_PROJECT'] = array(
		'IMAGE' => $rmImageFile ? $rmProjectAssetRoot.$rmImageFile : $rmDynamicImage,
		'DESCRIPTION' => $rmDescription ?: 'Подробнее о выполненном проекте и изготовленных для него изделиях.',
	);
}
unset($rmProjectItem);
