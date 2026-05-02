<?php
$dir = 'public/assets/images/products/';
$files = glob($dir . '*.webp');
foreach ($files as $file) {
    $size = getimagesize($file);
    echo "$file: {$size[0]}x{$size[1]}\n";
}
