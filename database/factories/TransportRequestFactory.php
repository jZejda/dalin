<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Models\TransportRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransportRequest>
 */
class TransportRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'direction' => TransportDirection::Both,
            'seats'     => 1,
            'status'    => TransportRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'      => TransportRequestStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TransportRequestStatus::Rejected,
        ]);
    }
}
