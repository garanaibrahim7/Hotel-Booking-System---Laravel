<?php

namespace App\Contracts;

interface SubscriptionProviderInterface
{
    public function processSubscribePlan($payload): array;

    public function createPlan(array $data): array;

    public function deletePlan(string $productId): array;

    public function updatePlan(string $productId, array $data): array;

    public function cancelSubscription(string $stripe_id): bool;

    public function updatePaymentMethod(array $payload): array;

    public function manageSubscription(array $payload): array;
    public function scheduleDowngrade(array $payload): array;
}
