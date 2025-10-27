<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('firebase_credentials.json'))
            ->withDatabaseUri('https://hydronova-f2401-default-rtdb.firebaseio.com/'); // <-- Correct URL

        $this->database = $factory->createDatabase();
    }

    public function getAll($collection)
    {
        $ref = $this->database->getReference($collection);
        $data = $ref->getValue();
        return $data ?? [];
    }

    public function getRef($collection)
    {
        return $this->database->getReference($collection);
    }
}
