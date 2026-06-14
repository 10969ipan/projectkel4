<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Resize all existing base64 profile photos to prevent Vercel payload too large error
        DB::table('users')->whereNotNull('profile_photo')->get()->each(function ($user) {
            $photo = $user->profile_photo;
            if (str_starts_with($photo, 'data:image/')) {
                // Check if GD library is available
                if (!function_exists('imagecreatefromstring')) {
                    // GD not available. If the image is large (> 200KB), clear it to prevent 500 error
                    if (strlen($photo) > 200000) {
                        DB::table('users')->where('id', $user->id)->update([
                            'profile_photo' => null
                        ]);
                    }
                    return;
                }

                // Parse base64
                try {
                    $parts = explode(',', $photo);
                    if (count($parts) < 2) return;
                    
                    $data = base64_decode($parts[1]);
                    if (!$data) return;

                    // Load image from string
                    $srcImage = @imagecreatefromstring($data);
                    if (!$srcImage) return;

                    $width = imagesx($srcImage);
                    $height = imagesy($srcImage);
                    
                    $maxWidth = 150;
                    $maxHeight = 150;
                    $ratio = $width / $height;

                    if ($width > $maxWidth || $height > $maxHeight) {
                        if ($maxWidth / $maxHeight > $ratio) {
                            $newHeight = $maxHeight;
                            $newWidth = $maxHeight * $ratio;
                        } else {
                            $newWidth = $maxWidth;
                            $newHeight = $maxWidth / $ratio;
                        }
                    } else {
                        $newWidth = $width;
                        $newHeight = $height;
                    }

                    $dstImage = imagecreatetruecolor($newWidth, $newHeight);
                    
                    // Handle transparency
                    $white = imagecolorallocate($dstImage, 255, 255, 255);
                    imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $white);

                    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    ob_start();
                    imagejpeg($dstImage, null, 75);
                    $compressedData = ob_get_clean();

                    imagedestroy($srcImage);
                    imagedestroy($dstImage);

                    $newBase64 = 'data:image/jpeg;base64,' . base64_encode($compressedData);

                    // Update in database
                    DB::table('users')->where('id', $user->id)->update([
                        'profile_photo' => $newBase64
                    ]);
                } catch (\Throwable $e) {
                    // Fail-safe: if something fails during image processing, we clear the profile_photo
                    // to prevent payload too large errors
                    DB::table('users')->where('id', $user->id)->update([
                        'profile_photo' => null
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed or possible
    }
};
