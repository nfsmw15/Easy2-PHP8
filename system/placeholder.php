<?php
declare(strict_types=1);
/**
 * Local placeholder image generator — replaces external placehold.it
 * Usage: placeholder.php?w=700&h=400
 *        placeholder.php?w=700&h=400&text=Hello&bg=cccccc&fg=666666
 */

$w    = max(1, min(3000, (int)($_GET['w']   ?? 300)));
$h    = max(1, min(3000, (int)($_GET['h']   ?? 200)));
$bg   = preg_replace('/[^0-9a-fA-F]/', '', $_GET['bg']   ?? 'cccccc');
$fg   = preg_replace('/[^0-9a-fA-F]/', '', $_GET['fg']   ?? '666666');
$text = strip_tags((string)($_GET['text'] ?? $w . 'x' . $h));

if (strlen($bg) !== 6) $bg = 'cccccc';
if (strlen($fg) !== 6) $fg = '666666';

$img = imagecreatetruecolor($w, $h);

$bgColor = imagecolorallocate(
    $img,
    hexdec(substr($bg, 0, 2)),
    hexdec(substr($bg, 2, 2)),
    hexdec(substr($bg, 4, 2))
);
$fgColor = imagecolorallocate(
    $img,
    hexdec(substr($fg, 0, 2)),
    hexdec(substr($fg, 2, 2)),
    hexdec(substr($fg, 4, 2))
);

imagefill($img, 0, 0, $bgColor);

// Draw text centered
$fontSize  = max(8, min(24, (int)($w / max(1, strlen($text)) * 1.5)));
$fontWidth  = imagefontwidth($fontSize > 4 ? 4 : $fontSize);
$fontHeight = imagefontheight($fontSize > 4 ? 4 : $fontSize);
$font = ($fontSize >= 14) ? 5 : (($fontSize >= 10) ? 4 : 3);
$textW = imagefontwidth($font) * strlen($text);
$textH = imagefontheight($font);
$x = max(0, (int)(($w - $textW) / 2));
$y = max(0, (int)(($h - $textH) / 2));

imagestring($img, $font, $x, $y, $text, $fgColor);

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
imagepng($img);
imagedestroy($img);
