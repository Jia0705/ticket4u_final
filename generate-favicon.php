<?php
// Generate favicon - 32x32 ticket icon
$size = 32;
$image = imagecreatetruecolor($size, $size);

// Purple gradient background
$purple = imagecolorallocate($image, 99, 102, 241);
$white = imagecolorallocate($image, 255, 255, 255);

// Fill background
imagefilledrectangle($image, 0, 0, $size, $size, $purple);

// Draw ticket shape (rounded rectangle)
imagefilledrectangle($image, 4, 8, $size-4, $size-8, $white);
imagefilledrectangle($image, 4, 8, 6, $size-8, $purple);
imagefilledrectangle($image, $size-6, 8, $size-4, $size-8, $purple);

// Output as PNG
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
