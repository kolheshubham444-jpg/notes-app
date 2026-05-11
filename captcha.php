<?php
session_start();

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$word  = '';
for ($i = 0; $i < 6; $i++) {
    $word .= $chars[random_int(0, strlen($chars) - 1)];
}
$_SESSION['captcha_word'] = $word;

$colors = ['#1e3a8a','#7c3aed','#be123c','#065f46','#92400e','#1d4ed8'];

$letters = '';
$x = 18;
foreach (str_split($word) as $i => $char) {
    $rotate = random_int(-18, 18);
    $y      = random_int(28, 38);
    $color  = $colors[$i % count($colors)];
    $size   = random_int(20, 26);
    $letters .= "<text x=\"$x\" y=\"$y\" fill=\"$color\" font-size=\"$size\" font-weight=\"bold\"
        font-family=\"Arial,sans-serif\" transform=\"rotate($rotate,$x,$y)\">$char</text>";
    $x += random_int(22, 28);
}

// noise lines
$lines = '';
for ($i = 0; $i < 5; $i++) {
    $x1 = random_int(0, 160); $y1 = random_int(0, 50);
    $x2 = random_int(0, 160); $y2 = random_int(0, 50);
    $lines .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\"
        stroke=\"#a5b4fc\" stroke-width=\"1.2\"/>";
}

// noise dots
$dots = '';
for ($i = 0; $i < 30; $i++) {
    $cx = random_int(0, 160); $cy = random_int(0, 50);
    $dots .= "<circle cx=\"$cx\" cy=\"$cy\" r=\"1.5\" fill=\"#c7d2fe\"/>";
}

header('Content-Type: image/svg+xml');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="50"
     style="background:#eef2ff;border-radius:6px;">
  $lines
  $dots
  $letters
</svg>
SVG;
