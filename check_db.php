<?php
require 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\DB::table('users')->where('email', 'admin@admin.com')->update([
    'password' => bcrypt('password')
]);

echo "Admin password updated to 'password'.\n";
