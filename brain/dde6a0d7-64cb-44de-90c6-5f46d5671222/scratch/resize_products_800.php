<?php
// Script to resize products to 800x800 for maximum quality
// Target: 800x800px, 90 quality

$dir = 'public/assets/images/products/';
$files = glob($dir . '*.webp');

echo "Starting image processing (Target: 800x800px)...\n";

foreach ($files as $file) {
    $img = @imagecreatefromwebp($file);
    if ($img) {
        $width = imagesx($img);
        $height = imagesy($img);
        
        $newWidth = 800;
        $newHeight = 800;
        
        $newImg = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Resize/Resample
        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save back with high quality (90)
        if (imagewebp($newImg, $file, 90)) {
            echo "Processed: $file ({$width}x{$height} -> {$newWidth}x{$newHeight})\n";
        } else {
            echo "Failed to save: $file\n";
        }
        
        imagedestroy($newImg);
        imagedestroy($img);
    } else {
        echo "Failed to load: $file\n";
    }
}

echo "Done.\n";
