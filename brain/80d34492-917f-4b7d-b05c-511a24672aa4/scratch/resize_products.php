<?php
// Script to resize products to 200x200 for grid optimization
$dir = 'public/assets/images/products/';
$files = glob($dir . '*.webp');

foreach ($files as $file) {
    $img = imagecreatefromwebp($file);
    if ($img) {
        $width = imagesx($img);
        $height = imagesy($img);
        
        // Target 400x400 for grid (Retina & Quick View support)
        if ($width != 400 || $height != 400) {
            $newImg = imagecreatetruecolor(400, 400);
            
            // Preserve transparency if any
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, 400, 400, $transparent);
            
            imagecopyresampled($newImg, $img, 0, 0, 0, 0, 400, 400, $width, $height);
            imagewebp($newImg, $file, 85); // Save back with 85 quality
            imagedestroy($newImg);
            echo "Resized: $file (400x400)\n";
        }
        imagedestroy($img);
    }
}
