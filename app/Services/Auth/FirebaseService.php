<?php

namespace App\Services\Auth;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;

class FirebaseService
{
    protected Auth $auth;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));

        $this->auth = $firebase->createAuth();
    }

    public function verifyUser(string $uid)
    {
        try {
            return $this->auth->getUser($uid);
        } catch (\Exception $e) {
            throw new \Exception('Invalid Firebase UID: '.$e->getMessage());
        }
    }

    public function verifyIdToken(string $idToken)
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (\Exception $e) {
            throw new \Exception('Invalid token: '.$e->getMessage());
        }
    }

    public function createUser(array $properties)
    {
        try {
            return $this->auth->createUser($properties);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Firebase user: '.$e->getMessage());
        }
    }

    public function updateUser(string $uid, array $properties)
    {
        try {
            return $this->auth->updateUser($uid, $properties);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update Firebase user: '.$e->getMessage());
        }
    }

    public function deleteUser(string $uid): void
    {
        try {
            $this->auth->deleteUser($uid);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete Firebase user: '.$e->getMessage());
        }
    }
}
