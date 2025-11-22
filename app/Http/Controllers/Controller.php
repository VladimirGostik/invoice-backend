<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * ✅ Získa user dáta z JWT bez DB dotazu
     */
    protected function getAuthenticatedUser(): array
    {
        $payload = auth()->payload();

        return [
            'id' => $payload->get('sub'), // Subject = user ID
            'role' => $payload->get('role'),
            'email' => $payload->get('email'), // ak je v tokene
        ];
    }

    protected function isUserSuperadmin(): bool
    {
        $user = $this->getAuthenticatedUser();
        return $user['role'] === 'superadmin';
    }
}
