<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/components/bitrix/news/projects/style.css');
$rmProjectImageRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/projects/';
$rmProductionImage = SITE_TEMPLATE_PATH.'/images/ralmaster/home/production-powder-line.webp';
?>
<main class="rm-project-detail rm-project-detail--generic rm-project-detail--brackets" data-rm-project-detail data-project-code="krepezhnye-kronshteyny">
	<section class="rm-project-detail__hero" data-rm-project-detail-section="hero" data-rm-project-section="hero">
		<div class="rm-project-detail__container">
			<nav class="rm-project-detail__breadcrumbs" aria-label="Хлебные крошки">
				<a href="/">Главная</a><span aria-hidden="true">/</span><a href="/projects/">Реализованные проекты</a><span aria-hidden="true">/</span><span>Крепёжные кронштейны</span>
			</nav>
			<div class="rm-project-detail__intro">
				<h1>Крепёжные кронштейны<br>по чертежам заказчика</h1>
				<p>Изготовление партии из 300 стальных кронштейнов: лазерная резка, гибка и порошковая окраска.</p>
			</div>
			<figure class="rm-project-detail__hero-media">
				<img src="<?=htmlspecialcharsbx($rmProjectImageRoot.'generic-project-hero.webp')?>" alt="Партия крепёжных кронштейнов по чертежам заказчика" fetchpriority="high">
				<figcaption>Готовая партия кронштейнов после порошковой окраски</figcaption>
			</figure>
		</div>
	</section>

	<section class="rm-project-detail__project" data-rm-project-detail-section="project" data-rm-project-section="project">
		<div class="rm-project-detail__container rm-project-detail__project-layout">
			<div class="rm-project-detail__project-copy">
				<h2>Задача заказчика</h2>
				<p>Изготовить крепёжные кронштейны по предоставленным чертежам. Детали предназначены для сборки металлических конструкций.</p>
				<p>В заказ входили раскрой листа, выполнение отверстий, гибка и окраска. Требовалось сохранить расположение крепёжных отверстий и геометрию деталей по документации.</p>
			</div>
			<aside class="rm-project-detail__passport" aria-label="Данные заказа">
				<h3>О заказе</h3>
				<dl>
					<div><dt>Изделие</dt><dd>Крепёжный кронштейн</dd></div>
					<div><dt>Материал</dt><dd>Листовая сталь, 3 мм</dd></div>
					<div><dt>Количество</dt><dd>300 шт.</dd></div>
					<div><dt>Выполненные работы</dt><dd>Лазерная резка, гибка, порошковая окраска</dd></div>
					<div><dt>Покрытие</dt><dd>Порошковая окраска, RAL 7016</dd></div>
					<div><dt>Формат заказа</dt><dd>Разовая партия</dd></div>
				</dl>
			</aside>
		</div>
	</section>

	<section class="rm-project-detail__work rm-project-detail__work--brackets" data-rm-project-detail-section="work" data-rm-project-section="work">
		<div class="rm-project-detail__container rm-project-detail__work-layout">
			<div>
				<h2>Что сделали</h2>
				<ol class="rm-project-detail__steps">
					<li><span>01</span><div><strong>Вырезали заготовки</strong><p>Выполнили лазерную резку контура и крепёжных отверстий по чертежам.</p></div></li>
					<li><span>02</span><div><strong>Выполнили гибку</strong><p>Сформировали углы и сохранили размеры каждой детали.</p></div></li>
					<li><span>03</span><div><strong>Подготовили и окрасили детали</strong><p>Подготовили поверхность и нанесли порошковое покрытие RAL 7016.</p></div></li>
				</ol>
			</div>
			<figure><img src="<?=htmlspecialcharsbx($rmProjectImageRoot.'generic-project-work.webp')?>" alt="Гибка металлического кронштейна" loading="lazy"><figcaption>Гибка деталей по размерам заказчика</figcaption></figure>
		</div>
	</section>

	<section class="rm-project-detail__result" data-rm-project-detail-section="result" data-rm-project-section="result">
		<div class="rm-project-detail__container">
			<div class="rm-project-detail__result-heading">
				<h2>Результат</h2>
				<p>Изготовили 300 одинаковых деталей с согласованной геометрией, расположением отверстий и стойким порошковым покрытием.</p>
			</div>
			<div class="rm-project-detail__result-grid">
				<figure><img src="<?=htmlspecialcharsbx($rmProjectImageRoot.'generic-project-result-batch.webp')?>" alt="Готовая партия кронштейнов" loading="lazy"><figcaption>Готовая партия</figcaption></figure>
				<figure><img src="<?=htmlspecialcharsbx($rmProjectImageRoot.'generic-project-result-detail.webp')?>" alt="Крепёжный кронштейн крупным планом" loading="lazy"><figcaption>Геометрия и покрытие детали</figcaption></figure>
			</div>
			<div class="rm-project-detail__result-links">
				<span>Подробнее о возможностях:</span>
				<div><a href="/services/">Металлообработка <span class="rm-ui-arrow" aria-hidden="true"></span></a><a href="/painting/">Порошковая окраска <span class="rm-ui-arrow" aria-hidden="true"></span></a></div>
			</div>
		</div>
	</section>

	<section class="rm-project-detail__cta" data-rm-project-detail-section="cta" data-rm-project-section="cta">
		<div class="rm-project-detail__container">
			<div class="rm-project-detail__cta-card">
				<h2>Нужен похожий заказ?</h2>
				<div class="rm-project-detail__cta-actions">
					<p>Пришлите чертежи или опишите задачу. Укажите количество изделий и необходимые работы.</p>
					<a class="rm-project-detail__cta-primary" href="/projects/#projects-estimate">Рассчитать похожий заказ <span class="rm-ui-arrow" aria-hidden="true"></span></a>
				</div>
			</div>
			<a class="rm-project-detail__cta-secondary" href="/projects/"><span aria-hidden="true">←</span> Все выполненные заказы</a>
		</div>
	</section>

	<section class="rm-projects-production-photo" data-rm-project-detail-section="production-photo" data-rm-project-section="production-photo" aria-label="Производство РАЛМАСТЕР">
		<img src="<?=htmlspecialcharsbx($rmProductionImage)?>" alt="Производство РАЛМАСТЕР" loading="lazy">
	</section>
</main>
