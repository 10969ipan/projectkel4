<?php
// Script to resize products to 200x200 for grid optimization
$dir = 'public/assets/images/products/';
$files = glob($dir . '*.webp');

foreach ($files as $file) {
    $img = imagecreatefromwebp($file);
    if ($img) {
        $width = imagesx($img);
        $height = imagesy($img);
        
        // Target 200x200 for grid
        if ($width > 200 || $height > 200) {
            $newImg = imagecreatetruecolor(200, 200);
            
            // Preserve transparency if any
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, 200, 200, $transparent);
            
            imagecopyresampled($newImg, $img, 0, 0, 0, 0, 200, 200, $width, $height);
            imagewebp($newImg, $file, 80); // Save back to same file with 80 quality
            imagedestroy($newImg);
            echo "Resized: $file\n";
        }
        imagedestroy($img);
    }
}
