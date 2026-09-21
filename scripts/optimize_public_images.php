<?php

/**
 * Génère des variantes WebP optimisées pour le front (accueil + conseils).
 * Usage : php scripts/optimize_public_images.php
 */

$base = dirname(__DIR__) . '/public/images';

function loadImage(string $srcPath)
{
    $info = @getimagesize($srcPath);
    if ($info === false) {
        return false;
    }
    switch ($info[2]) {
        case IMAGETYPE_PNG:
            $im = @imagecreatefrompng($srcPath);
            if ($im) {
                imagealphablending($im, true);
                imagesavealpha($im, true);
            }

            return $im;
        case IMAGETYPE_JPEG:
            return @imagecreatefromjpeg($srcPath);
        case IMAGETYPE_WEBP:
            return @imagecreatefromwebp($srcPath);
        default:
            return false;
    }
}

function saveWebp($srcPath, $destPath, int $quality = 82): bool
{
    $im = loadImage($srcPath);
    if (!$im) {
        return false;
    }
    $ok = imagewebp($im, $destPath, $quality);
    imagedestroy($im);

    return $ok;
}

function resizeAndSaveWebp($srcPath, $destPath, int $maxWidth, int $quality = 82): bool
{
    $im = loadImage($srcPath);
    if (!$im) {
        return false;
    }

    $w = imagesx($im);
    $h = imagesy($im);
    if ($w <= $maxWidth) {
        $nw = $w;
        $nh = $h;
    } else {
        $nw = $maxWidth;
        $nh = (int) round($h * ($maxWidth / $w));
    }

    $out = imagecreatetruecolor($nw, $nh);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefilledrectangle($out, 0, 0, $nw, $nh, $transparent);
    imagealphablending($out, true);
    imagecopyresampled($out, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagedestroy($im);

    $ok = imagewebp($out, $destPath, $quality);
    imagedestroy($out);

    return $ok;
}

$tasks = [
    ['in' => $base . '/hero-home-bg.png', 'out' => $base . '/hero-home-bg.webp', 'resize' => null],
    ['in' => $base . '/hero-home-bg.png', 'out' => $base . '/hero-home-bg-sm.webp', 'resize' => 640],
    ['in' => $base . '/hero-home-bureau.png', 'out' => $base . '/hero-home-bureau.webp', 'resize' => null],
    ['in' => $base . '/hero-home-bureau.png', 'out' => $base . '/hero-home-bureau-sm.webp', 'resize' => 640],
];

for ($i = 1; $i <= 4; $i++) {
    $p = $base . "/conseils-nutrition/conseil-{$i}.png";
    $tasks[] = ['in' => $p, 'out' => $base . "/conseils-nutrition/conseil-{$i}.webp", 'resize' => null];
    $tasks[] = ['in' => $p, 'out' => $base . "/conseils-nutrition/conseil-{$i}-sm.webp", 'resize' => 560];
}

foreach ($tasks as $t) {
    if (!is_file($t['in'])) {
        fwrite(STDERR, "Manquant : {$t['in']}\n");
        continue;
    }
    $before = filesize($t['in']);
    if ($t['resize']) {
        $ok = resizeAndSaveWebp($t['in'], $t['out'], $t['resize'], 82);
    } else {
        $ok = saveWebp($t['in'], $t['out'], 82);
    }
    if (!$ok) {
        fwrite(STDERR, "Échec : {$t['out']}\n");
        continue;
    }
    $after = filesize($t['out']);
    echo basename($t['in']) . ' → ' . basename($t['out']) . ' : ' . round($before / 1024, 1) . ' KB → ' . round($after / 1024, 1) . " KB\n";
}

echo "Terminé.\n";
