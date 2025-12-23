<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseService
{
    protected $database;
    protected $auth;

    public function __construct()
    {
        // Prefer config value (works with config:cache), fallback to default storage path
        $credentialsPath = config('firebase.projects.app.credentials.file')
            ?: storage_path('app/firebase_credentials.json');

        $factory = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->withDatabaseUri('https://hydronova-f2401-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->auth = $factory->createAuth();
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

    public function auth(): FirebaseAuth
    {
        return $this->auth;
    }
}
