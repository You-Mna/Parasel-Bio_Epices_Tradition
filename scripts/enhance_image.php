<?php
/**
 * Petit utilitaire pour améliorer une image (contraste + netteté).
 *
 * Usage:
 *   php scripts/enhance_image.php public/images/points-distribution-hero.png
 */
declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/enhance_image.php <path>\n");
    exit(2);
}

$path = $argv[1];
if (!is_file($path)) {
    fwrite(STDERR, "Fichier introuvable: {$path}\n");
    exit(2);
}

$info = @getimagesize($path);
if ($info === false) {
    fwrite(STDERR, "Impossible de lire l'image: {$path}\n");
    exit(2);
}

$type = $info[2] ?? null;
$im = false;
if ($type === IMAGETYPE_JPEG) {
    $im = @imagecreatefromjpeg($path);
} elseif ($type === IMAGETYPE_PNG) {
    $im = @imagecreatefrompng($path);
    if ($im) {
        imagealphablending($im, true);
        imagesavealpha($im, true);
    }
} elseif ($type === IMAGETYPE_WEBP) {
    $im = @imagecreatefromwebp($path);
} else {
    fwrite(STDERR, "Format non supporté.\n");
    exit(2);
}

if (!$im) {
    fwrite(STDERR, "Chargement GD échoué.\n");
    exit(1);
}

// Amélioration légère (évite l'effet "cramé")
@imagefilter($im, IMG_FILTER_CONTRAST, -6);
@imagefilter($im, IMG_FILTER_BRIGHTNESS, 2);

// Netteté via convolution (unsharp simple)
$kernel = [
    [0, -1, 0],
    [-1, 5.2, -1],
    [0, -1, 0],
];
@imageconvolution($im, $kernel, 1, 0);

$ok = false;
if ($type === IMAGETYPE_JPEG) {
    $ok = imagejpeg($im, $path, 88);
} elseif ($type === IMAGETYPE_PNG) {
    $ok = imagepng($im, $path, 6);
} elseif ($type === IMAGETYPE_WEBP) {
    $ok = imagewebp($im, $path, 86);
}

imagedestroy($im);

if (!$ok) {
    fwrite(STDERR, "Échec d'écriture: {$path}\n");
    exit(1);
}

echo "OK: {$path}\n";
