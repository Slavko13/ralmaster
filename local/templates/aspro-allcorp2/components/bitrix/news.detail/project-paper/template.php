<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);
$rmProject = $arResult['RM_PROJECT'];
$rmProjectButtons = CIBlock::GetPanelButtons($arResult['IBLOCK_ID'], $arResult['ID'], 0, array('SESSID' => false, 'CATALOG' => true));
$this->AddEditAction($arResult['ID'], $rmProjectButtons['edit']['edit_element']['ACTION_URL'], CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT'));
$this->AddDeleteAction($arResult['ID'], $rmProjectButtons['edit']['delete_element']['ACTION_URL'], CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
$rmProductionImage = SITE_TEMPLATE_PATH.'/images/ralmaster/home/production-powder-line.png';
?>

<main class="rm-project-detail<?=$rmProject['IS_SYMBOL'] ? ' rm-project-detail--symbol' : ' rm-project-detail--generic'?>" id="<?=$this->GetEditAreaId($arResult['ID'])?>" data-rm-project-detail data-project-code="<?=htmlspecialcharsbx($rmProject['CODE'])?>">
	<section class="rm-project-detail__hero" data-rm-project-detail-section="hero" data-rm-project-section="hero">
		<div class="rm-project-detail__container">
			<?if (!$rmProject['IS_SYMBOL']):?>
				<div class="rm-project-detail__notice" role="note"><strong>О данных проекта</strong><span>Дополнительные характеристики заказа в исходных данных не опубликованы; демонстрационные изображения ниже отмечены отдельно.</span></div>
			<?endif;?>
			<nav class="rm-project-detail__breadcrumbs" aria-label="Хлебные крошки">
				<a href="/">Главная</a><span aria-hidden="true">/</span><a href="/projects/">Реализованные проекты</a><span aria-hidden="true">/</span><span><?=htmlspecialcharsbx($rmProject['NAME'])?></span>
			</nav>
			<div class="rm-project-detail__intro">
				<h1><?=htmlspecialcharsbx($rmProject['TITLE'])?></h1>
				<p><?=htmlspecialcharsbx($rmProject['SUMMARY'])?></p>
				<?if ($rmProject['IS_SYMBOL']):?>
					<a class="rm-project-detail__product-link" href="<?=htmlspecialcharsbx($rmProject['PRODUCT_URL'])?>">Корзины для кондиционеров <span aria-hidden="true">↗</span></a>
				<?endif;?>
			</div>
			<?if ($rmProject['HERO_IMAGE']):?>
				<figure class="rm-project-detail__hero-media">
					<img src="<?=htmlspecialcharsbx($rmProject['HERO_IMAGE'])?>" alt="<?=htmlspecialcharsbx($rmProject['NAME'])?>">
					<?if (!$rmProject['IS_SYMBOL']):?><figcaption>Фотография из материалов проекта</figcaption><?endif;?>
				</figure>
			<?endif;?>
		</div>
	</section>

	<section class="rm-project-detail__project" data-rm-project-detail-section="project" data-rm-project-section="project">
		<div class="rm-project-detail__container rm-project-detail__project-layout">
			<div class="rm-project-detail__project-copy">
				<span class="rm-projects-kicker">Проект</span>
				<h2><?=$rmProject['IS_SYMBOL'] ? 'Задача проекта' : 'Что было выполнено'?></h2>
				<p><?=htmlspecialcharsbx($rmProject['TASK'])?></p>
			</div>
			<aside class="rm-project-detail__passport" aria-label="Данные заказа">
				<h3>О заказе</h3>
				<?if ($rmProject['PASSPORT']):?>
					<dl>
						<?foreach ($rmProject['PASSPORT'] as $rmPassportRow):?>
							<div><dt><?=htmlspecialcharsbx($rmPassportRow['LABEL'])?></dt><dd><?=htmlspecialcharsbx($rmPassportRow['VALUE'])?></dd></div>
						<?endforeach;?>
					</dl>
				<?else:?>
					<p class="rm-project-detail__passport-empty">Дополнительные характеристики заказа не опубликованы.</p>
				<?endif;?>
			</aside>
		</div>
	</section>

	<?if (!$rmProject['IS_SYMBOL']):?>
		<section class="rm-project-detail__work" data-rm-project-detail-section="work" data-rm-project-section="work">
			<div class="rm-project-detail__container rm-project-detail__work-layout">
				<div>
					<span class="rm-projects-kicker">Изготовление</span>
					<h2>Что изготовили</h2>
					<p><?=htmlspecialcharsbx($rmProject['SUMMARY'])?></p>
					<p class="rm-project-detail__disclosure">Изображение справа иллюстрирует производственные возможности и не является характеристикой этого заказа.</p>
				</div>
				<figure><img src="<?=htmlspecialcharsbx($rmProject['PRESENTATION']['WORK_IMAGE'])?>" alt="Производственный пример РАЛМАСТЕР" loading="lazy"><figcaption>Демонстрационный производственный пример</figcaption></figure>
			</div>
		</section>

		<section class="rm-project-detail__result" data-rm-project-detail-section="result" data-rm-project-section="result">
			<div class="rm-project-detail__container">
				<div class="rm-project-detail__result-heading">
					<div><span class="rm-projects-kicker">Результат</span><h2>Результат проекта</h2></div>
					<p>В исходных данных проекта нет отдельной фотогалереи. Ниже показаны демонстрационные изображения продукции РАЛМАСТЕР.</p>
				</div>
				<div class="rm-project-detail__result-grid">
					<?if ($rmProject['GALLERY']):?>
						<?foreach (array_slice($rmProject['GALLERY'], 0, 2) as $rmGalleryImage):?>
							<figure><img src="<?=htmlspecialcharsbx($rmGalleryImage)?>" alt="Материал проекта <?=htmlspecialcharsbx($rmProject['NAME'])?>" loading="lazy"><figcaption>Фотография из галереи проекта</figcaption></figure>
						<?endforeach;?>
					<?else:?>
						<?foreach ($rmProject['PRESENTATION']['RESULT_IMAGES'] as $rmResultImage):?>
							<figure><img src="<?=htmlspecialcharsbx($rmResultImage)?>" alt="Демонстрационный пример продукции РАЛМАСТЕР" loading="lazy"><figcaption>Демонстрационный пример продукции</figcaption></figure>
						<?endforeach;?>
					<?endif;?>
				</div>
			</div>
		</section>
	<?endif;?>

	<section class="rm-project-detail__cta" data-rm-project-detail-section="cta" data-rm-project-section="cta">
		<div class="rm-project-detail__container rm-project-detail__cta-card">
			<div>
				<h2><?=$rmProject['IS_SYMBOL'] ? 'Нужны корзины для вашего объекта?' : 'Нужен похожий заказ?'?></h2>
				<p>Пришлите чертёж или техническое задание — уточним возможность изготовления и подготовим расчёт.</p>
			</div>
			<div class="rm-project-detail__cta-actions">
				<a class="rm-project-detail__cta-primary" href="/projects/#projects-estimate">Рассчитать похожий заказ <span aria-hidden="true">↗</span></a>
				<a class="rm-project-detail__cta-secondary" href="/projects/">Все выполненные заказы</a>
			</div>
		</div>
	</section>

	<section class="rm-projects-production-photo" data-rm-project-detail-section="production-photo" data-rm-project-section="production-photo" aria-label="Производство РАЛМАСТЕР">
		<img src="<?=htmlspecialcharsbx($rmProductionImage)?>" alt="Производство РАЛМАСТЕР" loading="lazy">
		<span aria-hidden="true"></span>
	</section>
</main>
