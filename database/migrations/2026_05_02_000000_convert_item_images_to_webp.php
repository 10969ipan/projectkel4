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
        // Update all image_path entries in items table from .png to .webp
        DB::table('items')
            ->where('image_path', 'LIKE', '%.png')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, '.png', '.webp')")
            ]);

        // Also update any potential .jpg or .jpeg entries if needed
        DB::table('items')
            ->where('image_path', 'LIKE', '%.jpg')
            ->orWhere('image_path', 'LIKE', '%.jpeg')
            ->update([
                'image_path' => DB::raw("REPLACE(REPLACE(image_path, '.jpg', '.webp'), '.jpeg', '.webp')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert .webp back to .png if needed (optional)
        DB::table('items')
            ->where('image_path', 'LIKE', '%.webp')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, '.webp', '.png')")
            ]);
    }
};
