<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION, $arTheme;

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.css');

$rmDiscussEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};

$rmDiscussDefaults = array(
	'title' => 'Обсудить заказ',
	'intro' => 'Опишите задачу и приложите документацию, если она уже подготовлена.',
	'task_label' => 'Описание задачи',
	'task_placeholder' => 'Опишите задачу',
	'task_help' => '',
	'name_placeholder' => 'Как к вам обращаться',
	'contact_placeholder' => 'Укажите удобный способ связи',
	'upload_label' => 'Прикрепить файлы',
	'file_help' => 'Добавьте описание задачи или файл. Можно отправить и то и другое.',
	'consent_html' => 'Даю согласие на обработку персональных данных на условиях <a href="/include/licenses_detail.php">политики конфиденциальности</a>.',
	'button' => 'Отправить запрос',
	'note' => 'Менеджер свяжется с вами, чтобы уточнить детали.',
	'context' => 'website',
	'form_id' => 0,
	'height' => 850,
);
$rmDiscussOptions = array_merge(
	$rmDiscussDefaults,
	is_array($rmDiscussOptions ?? null) ? $rmDiscussOptions : array()
);
$rmDiscussId = 'rm-discuss-'.substr(md5((string)$rmDiscussOptions['context']), 0, 8);
$rmDiscussEstimateFormId = (int)$rmDiscussOptions['form_id'];
if ($rmDiscussEstimateFormId <= 0 && \Bitrix\Main\Loader::includeModule('form')) {
	$rmDiscussFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmDiscussFormResult && ($rmDiscussForm = $rmDiscussFormResult->Fetch())) {
		$rmDiscussEstimateFormId = (int)$rmDiscussForm['ID'];
	}
}
$rmDiscussHeight = max(640, min(1000, (int)$rmDiscussOptions['height']));
$rmDiscussFormHeight = $rmDiscussHeight - 260;
$rmDiscussFieldsHeight = $rmDiscussFormHeight - 120;
$rmDiscussTaskRowHeight = 148 + (trim((string)$rmDiscussOptions['task_help']) !== '' ? 48 : 0);
?>
<dialog class="rm-discuss" data-rm-discuss-dialog data-rm-discuss-variant="<?=$rmDiscussEscape($rmDiscussOptions['context'])?>" aria-labelledby="<?=$rmDiscussEscape($rmDiscussId)?>-title" style="--rm-discuss-height: <?=$rmDiscussHeight?>px; --rm-discuss-form-height: <?=$rmDiscussFormHeight?>px; --rm-discuss-fields-height: <?=$rmDiscussFieldsHeight?>px; --rm-discuss-task-row-height: <?=$rmDiscussTaskRowHeight?>px;">
	<div class="rm-discuss__card">
		<header class="rm-discuss__header">
			<h2 id="<?=$rmDiscussEscape($rmDiscussId)?>-title"><?=$rmDiscussEscape($rmDiscussOptions['title'])?></h2>
			<button type="button" data-rm-discuss-close aria-label="Закрыть форму">×</button>
		</header>
		<div class="rm-discuss__intro"><?=$rmDiscussEscape($rmDiscussOptions['intro'])?></div>
		<div class="rm-discuss__form" data-rm-estimate-form data-rm-estimate-context="<?=$rmDiscussEscape($rmDiscussOptions['context'])?>" aria-label="<?=$rmDiscussEscape($rmDiscussOptions['title'])?>">
			<?if ($rmDiscussEstimateFormId > 0):?>
				<?$APPLICATION->IncludeComponent(
					'aspro:form.allcorp2',
					'contacts',
					array(
						'IBLOCK_TYPE' => 'aspro_allcorp2_form',
						'IBLOCK_ID' => $rmDiscussEstimateFormId,
						'USE_CAPTCHA' => 'N',
						'IS_PLACEHOLDER' => 'Y',
						'SUCCESS_MESSAGE' => 'Спасибо! Ваш запрос отправлен.',
						'SEND_BUTTON_NAME' => $rmDiscussOptions['button'],
						'SEND_BUTTON_CLASS' => 'btn btn-default',
						'DISPLAY_CLOSE_BUTTON' => 'N',
						'SHOW_LICENCE' => $arTheme['SHOW_LICENCE']['VALUE'],
						'LICENCE_TEXT' => $arTheme['SHOW_LICENCE']['DEPENDENT_PARAMS']['LICENCE_TEXT']['VALUE'],
						'AJAX_MODE' => 'Y',
						'AJAX_OPTION_JUMP' => 'N',
						'AJAX_OPTION_STYLE' => 'Y',
						'AJAX_OPTION_HISTORY' => 'N',
						'CACHE_TYPE' => 'A',
						'CACHE_TIME' => '100000',
						'AJAX_OPTION_ADDITIONAL' => $rmDiscussId,
					),
					false
				);?>
			<?else:?>
				<p role="status">Форма временно недоступна. Свяжитесь с нами по телефону или электронной почте.</p>
			<?endif;?>
		</div>
		<p class="rm-discuss__note"><?=$rmDiscussEscape($rmDiscussOptions['note'])?></p>
	</div>
</dialog>
<script>
(function () {
	'use strict';
	var dialog = document.querySelector('[data-rm-discuss-variant="<?=CUtil::JSEscape($rmDiscussOptions['context'])?>"]');
	if (!dialog) { return; }
	var idPrefix = '<?=CUtil::JSEscape($rmDiscussId)?>';
	function namespaceDialogIds() {
		var idMap = {};
		dialog.querySelectorAll('[id]').forEach(function (node) {
			var oldId = node.id;
			if (!oldId || oldId.indexOf(idPrefix + '-') === 0 || /^comp_|^bxajaxid_/.test(oldId)) { return; }
			var newId = idPrefix + '-' + oldId.replace(/[^A-Za-z0-9_-]+/g, '-');
			idMap[oldId] = newId;
			if (oldId === 'contacs_form') { node.classList.add('rm-discuss__legacy-contact-form'); }
			node.id = newId;
		});
		['for', 'aria-labelledby', 'aria-describedby', 'aria-controls'].forEach(function (attribute) {
			dialog.querySelectorAll('[' + attribute + ']').forEach(function (node) {
				var value = node.getAttribute(attribute);
				if (!value) { return; }
				node.setAttribute(attribute, value.split(/\s+/).map(function (token) { return idMap[token] || token; }).join(' '));
			});
		});
		dialog.querySelectorAll('a[href^="#"]').forEach(function (link) {
			var oldId = link.getAttribute('href').slice(1);
			if (idMap[oldId]) { link.setAttribute('href', '#' + idMap[oldId]); }
		});
	}
	namespaceDialogIds();
	document.querySelectorAll('[data-rm-discuss-open]').forEach(function (trigger) {
		var triggerContext = trigger.getAttribute('data-rm-discuss-open');
		if (triggerContext && triggerContext !== '<?=CUtil::JSEscape($rmDiscussOptions['context'])?>') { return; }
		trigger.addEventListener('click', function () {
			if (typeof dialog.showModal === 'function') {
				dialog.showModal();
			} else {
				dialog.setAttribute('open', '');
			}
		});
	});
	dialog.querySelectorAll('[data-rm-discuss-close]').forEach(function (button) {
		button.addEventListener('click', function () {
			if (typeof dialog.close === 'function') { dialog.close(); }
			else { dialog.removeAttribute('open'); }
		});
	});
	dialog.addEventListener('click', function (event) {
		if (event.target !== dialog) { return; }
		if (typeof dialog.close === 'function') { dialog.close(); }
		else { dialog.removeAttribute('open'); }
	});
	var taskLabelText = '<?=CUtil::JSEscape($rmDiscussOptions['task_label'])?>';
	var taskPlaceholder = '<?=CUtil::JSEscape($rmDiscussOptions['task_placeholder'])?>';
	var taskHelp = '<?=CUtil::JSEscape($rmDiscussOptions['task_help'])?>';
	var namePlaceholder = '<?=CUtil::JSEscape($rmDiscussOptions['name_placeholder'])?>';
	var contactPlaceholder = '<?=CUtil::JSEscape($rmDiscussOptions['contact_placeholder'])?>';
	var uploadLabel = '<?=CUtil::JSEscape($rmDiscussOptions['upload_label'])?>';
	var fileHelp = '<?=CUtil::JSEscape($rmDiscussOptions['file_help'])?>';
	var consentHtml = <?=\Bitrix\Main\Web\Json::encode((string)$rmDiscussOptions['consent_html'])?>;
	var buttonText = '<?=CUtil::JSEscape($rmDiscussOptions['button'])?>';
	function decorateLabel(group, text) {
		if (!group || !text) { return; }
		var span = group.querySelector('label span');
		if (!span || span.getAttribute('data-rm-label-text') === text) { return; }
		var required = span.querySelector('.required-star');
		span.textContent = text;
		if (required) {
			span.appendChild(document.createTextNode(' '));
			span.appendChild(required);
		}
		span.setAttribute('data-rm-label-text', text);
	}
	function decoratePlaceholder(group, placeholder) {
		var input = group ? group.querySelector('input, textarea') : null;
		if (input && input.placeholder !== placeholder) { input.placeholder = placeholder; }
	}
	function decorateForm() {
		var name = dialog.querySelector('[data-SID="NAME"]');
		decorateLabel(name, 'Ваше имя');
		decoratePlaceholder(name, namePlaceholder);
		var contact = dialog.querySelector('[data-SID="CONTACT"]');
		decorateLabel(contact, 'Телефон или электронная почта');
		decoratePlaceholder(contact, contactPlaceholder);
		var task = dialog.querySelector('[data-SID="TASK"]');
		if (task) {
			decorateLabel(task, taskLabelText);
			decoratePlaceholder(task, taskPlaceholder);
			var taskGroup = task.querySelector('.form-group');
			if (taskGroup && taskHelp && taskGroup.getAttribute('data-rm-task-help') !== taskHelp) {
				taskGroup.setAttribute('data-rm-task-help', taskHelp);
			}
		}
		var file = dialog.querySelector('[data-SID="FILE"]');
		var fileGroup = file ? file.querySelector('.form-group') : null;
		if (fileGroup && fileGroup.getAttribute('data-rm-file-help') !== fileHelp) {
			fileGroup.setAttribute('data-rm-file-help', fileHelp);
		}
		var upload = dialog.querySelector('.uploader .filename');
		if (upload) {
			if (upload.getAttribute('data-rm-upload-label') !== uploadLabel) {
				upload.setAttribute('data-rm-upload-label', uploadLabel);
			}
		}
		var submit = dialog.querySelector('input[type="submit"]');
		if (submit && submit.getAttribute('aria-busy') !== 'true' && submit.value !== buttonText) {
			submit.value = buttonText;
		}
		var consent = dialog.querySelector('.licence_block label');
		if (consent && consent.getAttribute('data-rm-consent-decorated') !== 'true') {
			consent.innerHTML = consentHtml;
			consent.setAttribute('data-rm-consent-decorated', 'true');
		}
	}
	function refreshDialog() {
		namespaceDialogIds();
		decorateForm();
	}
	refreshDialog();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', refreshDialog, { once: true });
	}
	window.addEventListener('load', refreshDialog, { once: true });
	if (window.MutationObserver) {
		var lifecycleObserver = new MutationObserver(function () {
			lifecycleObserver.disconnect();
			refreshDialog();
			lifecycleObserver.observe(dialog, { childList: true, subtree: true });
		});
		lifecycleObserver.observe(dialog, { childList: true, subtree: true });
	}
	dialog.addEventListener('submit', function () {
		var submit = dialog.querySelector('input[type="submit"]');
		if (!submit || submit.disabled) { return; }
		submit.disabled = true;
		submit.setAttribute('aria-busy', 'true');
		submit.value = 'Отправляем…';
		window.setTimeout(function () {
			if (!submit.isConnected || dialog.querySelector('.form.contacts.success')) { return; }
			submit.disabled = false;
			submit.removeAttribute('aria-busy');
			submit.value = buttonText;
		}, 20000);
	}, true);
	window.setTimeout(refreshDialog, 250);
}());
</script>
