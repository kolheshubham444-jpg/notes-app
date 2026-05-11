<?php
require 'db.php'; // starts session

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$word  = '';
for ($i = 0; $i < 6; $i++) {
    $word .= $chars[random_int(0, strlen($chars) - 1)];
}
$_SESSION['captcha_word'] = $word;

$width  = 160;
$height = 50;
$img    = imagecreatetruecolor($width, $height);

$bg   = imagecolorallocate($img, 240, 240, 255);
$fg   = imagecolorallocate($img, 30,  30,  120);
$noise = imagecolorallocate($img, 180, 180, 210);

imagefill($img, 0, 0, $bg);

// noise dots
for ($i = 0; $i < 600; $i++) {
    imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $noise);
}

// noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($img,
        random_int(0, $width), random_int(0, $height),
        random_int(0, $width), random_int(0, $height),
        $noise
    );
}

// draw each character with random vertical offset
$font_size = 5; // built-in font size (1–5)
$x = 14;
foreach (str_split($word) as $char) {
    $y = random_int(10, $height - 20);
    imagechar($img, $font_size, $x, $y, $char, $fg);
    $x += 22 + random_int(-2, 4);
}

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
imagepng($img);
imagedestroy($img);
