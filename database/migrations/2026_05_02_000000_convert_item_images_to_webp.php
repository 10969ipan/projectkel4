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
        // Update wellness_articles image_path to .webp
        if (Schema::hasTable('wellness_articles')) {
            DB::table('wellness_articles')->get()->each(function ($article) {
                $newPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $article->image_path);
                if ($newPath !== $article->image_path) {
                    DB::table('wellness_articles')
                        ->where('id', $article->id)
                        ->update(['image_path' => $newPath]);
                }
            });
        }

        // Update items image_path to .webp
        DB::table('items')->get()->each(function ($item) {
            $newPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $item->image_path);
            if ($newPath !== $item->image_path) {
                DB::table('items')
                    ->where('id', $item->id)
                    ->update(['image_path' => $newPath]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a bit tricky to reverse perfectly without knowing original extensions, 
        // but we can assume common ones if needed. For now, we'll leave it as is or try to guess.
    }
};
