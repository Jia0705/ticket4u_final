<?php
// Simple image placeholder generator
// Creates a gradient image with text

$width = 800;
$height = 600;
$text = isset($_GET['text']) ? $_GET['text'] : 'Event Image';

// Create image
$image = imagecreatetruecolor($width, $height);

// Create gradient colors
$color1 = imagecolorallocate($image, 102, 126, 234); // #667eea
$color2 = imagecolorallocate($image, 118, 75, 162);  // #764ba2
$white = imagecolorallocate($image, 255, 255, 255);

// Fill with gradient
for ($y = 0; $y < $height; $y++) {
    $ratio = $y / $height;
    $r = 102 + ($ratio * (118 - 102));
    $g = 126 + ($ratio * (75 - 126));
    $b = 234 + ($ratio * (162 - 234));
    $color = imagecolorallocate($image, $r, $g, $b);
    imagefilledrectangle($image, 0, $y, $width, $y + 1, $color);
}

// Add text
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;
imagestring($image, $font_size, $x, $y, $text, $white);

// Add size info
$size_text = $width . ' x ' . $height;
$size_width = imagefontwidth(3) * strlen($size_text);
$size_x = ($width - $size_width) / 2;
$size_y = $y + 30;
imagestring($image, 3, $size_x, $size_y, $size_text, $white);

// Output
header('Content-Type: image/jpeg');
imagejpeg($image, null, 90);
imagedestroy($image);
