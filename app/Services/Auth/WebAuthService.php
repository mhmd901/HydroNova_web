<?php

namespace App\Services\Auth;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebAuthService
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function findCustomerByEmail(string $email): ?array
    {
        $snapshot = $this->firebase->getRef('customers')
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if (!$snapshot || !is_array($snapshot)) {
            return null;
        }

        $record = reset($snapshot);
        if (!is_array($record)) {
            return null;
        }

        if (!isset($record['uid'])) {
            $record['uid'] = array_key_first($snapshot);
        }

        return $record;
    }

    public function findCustomerByUid(string $uid): ?array
    {
        $record = $this->firebase->getRef("customers/{$uid}")->getValue();

        if (!$record || !is_array($record)) {
            return null;
        }

        if (!isset($record['uid'])) {
            $record['uid'] = $uid;
        }

        return $record;
    }

    public function createCustomer(string $name, string $email, string $password): array
    {
        $uid = 'cust_' . Str::lower(Str::random(10));
        $record = [
            'uid' => $uid,
            'full_name' => $name,
            'email' => $email,
            'password_hash' => Hash::make($password),
            'created_at' => now()->toDateTimeString(),
        ];

        $this->firebase->getRef("customers/{$uid}")->set($record);

        return $record;
    }

    public function verifyPassword(array $customer, string $password): bool
    {
        $hash = $customer['password_hash'] ?? null;
        return is_string($hash) && Hash::check($password, $hash);
    }

    public function updateProfile(string $uid, string $name, string $email): array
    {
        $existing = $this->findCustomerByEmail($email);
        if ($existing && ($existing['uid'] ?? null) !== $uid) {
            return ['ok' => false, 'error' => 'email_taken'];
        }

        $data = [
            'full_name' => $name,
            'email' => $email,
            'updated_at' => now()->toDateTimeString(),
        ];

        $this->firebase->getRef("customers/{$uid}")->update($data);

        $record = $this->findCustomerByUid($uid) ?? array_merge(['uid' => $uid], $data);

        return ['ok' => true, 'record' => $record];
    }

    public function updatePassword(string $uid, string $password): void
    {
        $this->firebase->getRef("customers/{$uid}")->update([
            'password_hash' => Hash::make($password),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}
