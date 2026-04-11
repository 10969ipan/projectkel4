<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Perbaiki data: User yang merupakan Admin atau Staff lama (StoreRole='customer' karena default migration)
// Kita harus memisahkan mana yang benar-benar pelanggan baru dan mana yang user sistem.
// User sistem biasanya punya role 'admin' atau sudah ada sebelum fitur 'customer' ditambahkan.

$affected = User::where('role', 'admin')
                ->where('store_role', 'customer')
                ->update(['store_role' => null]);

echo "Fixed {$affected} admin users.\n";

// Untuk staff, ini agak tricky jika mereka juga pelanggan. 
// Tapi untuk saat ini, kita anggap staff yang sudah ada adalah staff sistem.
// Kita bisa cek date atau ID jika perlu, tapi cara paling aman:
// User yang email-nya tidak didaftarkan via StoreAuthController biasanya staff sistem.

$systemUsers = User::where('role', 'staff')
                   ->where('store_role', 'customer')
                   ->update(['store_role' => null]);

echo "Fixed {$systemUsers} staff users.\n";
