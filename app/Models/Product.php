<?php

namespace App\Models;

use App\Services\FirebaseService;

class Product
{
    protected $ref;
    protected $service;

    public function __construct()
    {
        $this->service = new FirebaseService();
        $this->ref = $this->service->database()->getReference('products');
    }

    public function all(): array
    {
        $value = $this->ref->getValue();
        return $value ?? [];
    }

    public function find(string $id): ?array
    {
        $value = $this->service->database()->getReference("products/{$id}")->getValue();
        return $value ?: null;
    }

    public function create(array $data)
    {
        return $this->ref->push($data);
    }

    public function update(string $id, array $data)
    {
        return $this->service->database()->getReference("products/{$id}")->update($data);
    }

    public function delete(string $id)
    {
        return $this->service->database()->getReference("products/{$id}")->remove();
    }
}
