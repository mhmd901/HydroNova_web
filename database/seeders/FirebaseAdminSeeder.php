<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\FirebaseService;

class FirebaseAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firebase = app(FirebaseService::class);

        $firebase->getRef('settings/admin')->set([
            'username' => 'admin',
            'password' => bcrypt('admin'), // store hashed password
            'role' => 'superadmin',
        ]);

        echo "✅ Firebase admin credentials seeded successfully.\n";
    }
}
