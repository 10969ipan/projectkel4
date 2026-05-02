<?php
// Script to resize products to 400x400 for better quality (Retina/Quick View)
// Target: 400x400px, 85 quality for balance

$dir = 'public/assets/images/products/';
$files = glob($dir . '*.webp');

echo "Starting image processing (Target: 400x400px)...\n";

foreach ($files as $file) {
    $img = @imagecreatefromwebp($file);
    if ($img) {
        $width = imagesx($img);
        $height = imagesy($img);
        
        // We always target 400x400 to ensure uniformity
        // Even if it's smaller, we'll upscale (user's choice for "sharpness" on high-res displays, 
        // though usually downscaling is better)
        
        $newWidth = 400;
        $newHeight = 400;
        
        $newImg = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Resize/Resample
        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save back with slightly higher quality than before (85 instead of 80)
        if (imagewebp($newImg, $file, 85)) {
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
