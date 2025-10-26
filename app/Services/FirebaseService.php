<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected Database $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase_credentials.json'))
            ->withDatabaseUri('https://hydronova-f2401-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function database(): Database
    {
        return $this->database;
    }
}
