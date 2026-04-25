<?php

// Bridge Laravel ke Vercel
// Pindahkan storage dan cache ke /tmp agar bisa ditulis (Writable)
putenv('APP_STORAGE=/tmp');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');

require __DIR__ . '/../public/index.php';
