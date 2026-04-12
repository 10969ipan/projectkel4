<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$order = App\Models\StoreOrder::with('items.item:id,name')->latest()->first();
if ($order) {
    $names = $order->items->pluck('item.name')->filter()->toArray();
    echo "Latest order #" . $order->id . " items:\n";
    foreach ($names as $n) echo "  - " . $n . "\n";
    
    // Test matching
    $articles = App\Models\HealthArticle::where('is_active', true)->get();
    echo "\nMatching articles:\n";
    foreach ($articles as $a) {
        $keyword = strtolower($a->keyword ?? '');
        foreach ($names as $name) {
            $nameLower = strtolower($name);
            if (str_contains($nameLower, $keyword) || str_contains($keyword, $nameLower)) {
                echo "  MATCH: [{$a->keyword}] => [{$name}] => {$a->title}\n";
            }
        }
    }
} else {
    echo "No orders found.\n";
}
