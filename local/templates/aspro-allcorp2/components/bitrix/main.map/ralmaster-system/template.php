<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (empty($arResult['arMap']) || !is_array($arResult['arMap'])) {
	echo '<div class="rm-system-note">Карта сайта пока не содержит разделов.</div>';
	return;
}

$rmPreviousLevel = 0;
$rmFirstItem = true;
?>
<nav aria-label="Карта сайта" data-rm-site-map>
	<ul class="rm-system-map">
		<?php foreach ($arResult['arMap'] as $rmMapItem) {
			$rmLevel = max(0, (int)$rmMapItem['LEVEL']);
			if (!$rmFirstItem) {
				if ($rmLevel > $rmPreviousLevel) {
					echo '<ul>';
				} elseif ($rmLevel === $rmPreviousLevel) {
					echo '</li>';
				} else {
					echo '</li>'.str_repeat('</ul></li>', $rmPreviousLevel - $rmLevel);
				}
			}

			$rmPath = htmlspecialcharsbx($rmMapItem['FULL_PATH'], ENT_COMPAT, false);
			$rmName = htmlspecialcharsbx($rmMapItem['NAME'], ENT_COMPAT, false);
			$rmDescription = htmlspecialcharsbx($rmMapItem['DESCRIPTION'], ENT_COMPAT, false);
			?>
			<li><a href="<?=$rmPath?>"><?=$rmName?></a><?php if ($arParams['SHOW_DESCRIPTION'] === 'Y' && $rmDescription !== '') { ?><p><?=$rmDescription?></p><?php } ?>
			<?php
			$rmPreviousLevel = $rmLevel;
			$rmFirstItem = false;
		} ?>
		</li><?=str_repeat('</ul></li>', $rmPreviousLevel)?>
	</ul>
</nav>
