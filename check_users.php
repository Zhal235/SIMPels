<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Check users
$users = App\Models\User::all(['id', 'name', 'email']);
echo "Total users: " . $users->count() . PHP_EOL;

foreach($users as $user) {
    echo $user->id . " - " . $user->name . " (" . $user->email . ")" . PHP_EOL;
    
    // Check roles
    if (method_exists($user, 'getRoleNames')) {
        $roles = $user->getRoleNames();
        echo "  Roles: " . $roles->implode(', ') . PHP_EOL;
    }
}
