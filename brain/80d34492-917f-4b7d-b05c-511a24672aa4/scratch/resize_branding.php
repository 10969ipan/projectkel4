<?php
// Script to resize branding images for desktop optimization
$dir = 'public/assets/images/branding/';
$brandingFiles = ['dokter.webp', 'dokter1.webp'];

foreach ($brandingFiles as $filename) {
    $file = $dir . $filename;
    if (file_exists($file)) {
        $img = imagecreatefromwebp($file);
        if ($img) {
            $width = imagesx($img);
            $height = imagesy($img);
            
            // Target 450x450 for desktop hero
            if ($width > 450 || $height > 450) {
                $newImg = imagecreatetruecolor(450, 450);
                
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                imagefilledrectangle($newImg, 0, 0, 450, 450, $transparent);
                
                imagecopyresampled($newImg, $img, 0, 0, 0, 0, 450, 450, $width, $height);
                imagewebp($newImg, $file, 85); 
                imagedestroy($newImg);
                echo "Resized branding: $file\n";
            }
            imagedestroy($img);
        }
    }
}
