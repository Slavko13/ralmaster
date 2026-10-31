<?php

declare(strict_types=1);

/**
 * Generate the deliberately cropped mobile hero used by the redesigned home.
 *
 * Run inside the project PHP image (GD with WebP support):
 *   docker exec ralmaster-redesign-web-1 php /var/www/html/scripts/generate-responsive-images.php
 */

$projectRoot = dirname(__DIR__);
$sourcePath = $projectRoot.'/local/templates/aspro-allcorp2/images/ralmaster/home/hero-laser-cutting.webp';
$targetPath = $projectRoot.'/local/templates/aspro-allcorp2/images/ralmaster/home/hero-laser-cutting-mobile.webp';

$source = imagecreatefromwebp($sourcePath);
if (!$source) {
	throw new RuntimeException('Unable to read '.$sourcePath);
}

$sourceWidth = imagesx($source);
$sourceHeight = imagesy($source);
$cropWidth = min(768, $sourceWidth);
$horizontalFocus = 0.64;
$cropX = (int)round(($sourceWidth - $cropWidth) * $horizontalFocus);

$mobile = imagecreatetruecolor($cropWidth, $sourceHeight);
if (!$mobile) {
	imagedestroy($source);
	throw new RuntimeException('Unable to allocate the mobile hero canvas');
}

imagecopy($mobile, $source, 0, 0, $cropX, 0, $cropWidth, $sourceHeight);

if (!imagewebp($mobile, $targetPath, 80)) {
	imagedestroy($mobile);
	imagedestroy($source);
	throw new RuntimeException('Unable to write '.$targetPath);
}

imagedestroy($mobile);
imagedestroy($source);

$size = filesize($targetPath);
echo sprintf(
	"Generated %s (%dx%d, %d bytes)\n",
	$targetPath,
	$cropWidth,
	$sourceHeight,
	$size === false ? 0 : $size
);

$cardImages = array(
	'powder-flat-panels.webp',
	'powder-profiles.webp',
	'facade-cassettes.webp',
	'air-conditioner-baskets.webp',
	'custom-metalwork.webp',
	'hero-laser-cutting.webp',
	'metal-bending.webp',
	'welding.webp',
);

foreach ($cardImages as $fileName) {
	$cardSourcePath = $projectRoot.'/local/templates/aspro-allcorp2/images/ralmaster/home/'.$fileName;
	$cardTargetPath = preg_replace('/\.webp$/', '-768.webp', $cardSourcePath);
	$cardSource = imagecreatefromwebp($cardSourcePath);
	if (!$cardSource || !$cardTargetPath) {
		throw new RuntimeException('Unable to prepare responsive variant for '.$cardSourcePath);
	}

	$cardSourceWidth = imagesx($cardSource);
	$cardSourceHeight = imagesy($cardSource);
	$cardTargetWidth = min(768, $cardSourceWidth);
	$cardTargetHeight = (int)round($cardSourceHeight * ($cardTargetWidth / $cardSourceWidth));
	$cardTarget = imagecreatetruecolor($cardTargetWidth, $cardTargetHeight);
	if (!$cardTarget) {
		imagedestroy($cardSource);
		throw new RuntimeException('Unable to allocate a responsive image canvas');
	}

	imagecopyresampled(
		$cardTarget,
		$cardSource,
		0,
		0,
		0,
		0,
		$cardTargetWidth,
		$cardTargetHeight,
		$cardSourceWidth,
		$cardSourceHeight
	);

	if (!imagewebp($cardTarget, $cardTargetPath, 80)) {
		imagedestroy($cardTarget);
		imagedestroy($cardSource);
		throw new RuntimeException('Unable to write '.$cardTargetPath);
	}

	$cardSize = filesize($cardTargetPath);
	printf(
		"Generated %s (%dx%d, %d bytes)\n",
		$cardTargetPath,
		$cardTargetWidth,
		$cardTargetHeight,
		$cardSize === false ? 0 : $cardSize
	);

	imagedestroy($cardTarget);
	imagedestroy($cardSource);
}
