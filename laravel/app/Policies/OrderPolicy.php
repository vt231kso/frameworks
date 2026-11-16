<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Викликається перед будь-якою іншою перевіркою.
     * Якщо це менеджер або адмін — дозволяємо доступ одразу.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isManager() ? true : null;
    }

    /**
     * Перевірка права перегляду конкретного замовлення для звичайного клієнта.
     */
    public function view(User $user, Order $order): bool
    {

        $clientProfile = Client::where('email', $user->email)->first();

        return $clientProfile && $order->client_id === $clientProfile->id;
    }
}
