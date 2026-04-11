<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

use App\Models\User;

$users = User::all();
echo "TOTAL USERS: " . $users->count() . "\n";
foreach($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | StoreRole: {$u->store_role}\n";
}
