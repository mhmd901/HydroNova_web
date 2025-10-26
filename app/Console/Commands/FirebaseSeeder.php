<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;

class FirebaseSeeder extends Command
{
    protected $signature = 'firebase:seed';
    protected $description = 'Seed Firebase with sample Plans and Products';

    public function handle()
    {
        $firebase = new FirebaseService();

        // Seed sample Plans
        $plansRef = $firebase->database()->getReference('plans');
        $plansRef->push([
            'name' => 'Basic Plan',
            'price' => 10,
            'description' => 'This is a basic plan'
        ]);
        $plansRef->push([
            'name' => 'Pro Plan',
            'price' => 25,
            'description' => 'This is a professional plan'
        ]);

        // Seed sample Products
        $productsRef = $firebase->database()->getReference('products');
        $productsRef->push([
            'name' => 'Widget',
            'price' => 15,
            'description' => 'A useful widget'
        ]);
        $productsRef->push([
            'name' => 'Gadget',
            'price' => 30,
            'description' => 'A high-quality gadget'
        ]);

        $this->info('Firebase seeded successfully!');
    }
}
