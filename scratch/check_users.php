<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::select('id', 'name', 'email', 'role', 'store_role')->get();
foreach($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role} | StoreRole: " . ($user->store_role ?? 'NULL') . "\n";
}
