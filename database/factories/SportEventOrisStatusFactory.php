<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Defaults to an unpaid settlement before the start list is out.
 *
 * @extends Factory<SportEventOrisStatus>
 */
class SportEventOrisStatusFactory extends Factory
{
    public function definition(): array
    {
        $feeEntry = $this->faker->numberBetween(4, 30) * 20;
        $feeSi = $this->faker->randomElement([0, 0, 40, 80]);

        return [
            'sport_event_id' => SportEvent::factory(),
            'bank_account' => $this->faker->numerify('##########').'/'.$this->faker->randomElement(['0100', '0300', '0800', '2010', '0600']),
            'starts_count' => 0,
            'results_count' => 0,
            'fee_entry' => $feeEntry,
            'fee_si' => $feeSi,
            'fee_service' => 0,
            'fee_total' => $feeEntry + $feeSi,
            'paid' => 0,
            'to_be_paid' => $feeEntry + $feeSi,
            'currency' => 'CZK',
            'payment_vs' => $this->faker->numerify('26#####'),
            'fetched_at' => now(),
        ];
    }

    public function partiallyPaid(float $paid): static
    {
        return $this->state(fn (array $attributes): array => [
            'paid' => $paid,
            'to_be_paid' => $attributes['fee_total'] - $paid,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'paid' => $attributes['fee_total'],
            'to_be_paid' => 0,
        ]);
    }

    public function withServices(float $feeService): static
    {
        return $this->state(fn (array $attributes): array => [
            'fee_service' => $feeService,
            'fee_total' => $attributes['fee_total'] + $feeService,
            'to_be_paid' => $attributes['to_be_paid'] + $feeService,
        ]);
    }

    public function published(bool $results = false): static
    {
        return $this->state(fn (): array => [
            'starts_count' => $this->faker->numberBetween(150, 900),
            'results_count' => $results ? $this->faker->numberBetween(150, 900) : 0,
        ]);
    }

    /**
     * The club has no entries in ORIS — no settlement row.
     */
    public function withoutClubBalance(): static
    {
        return $this->state(fn (): array => [
            'fee_entry' => null,
            'fee_si' => null,
            'fee_service' => null,
            'fee_total' => null,
            'paid' => null,
            'to_be_paid' => null,
            'payment_vs' => null,
        ]);
    }
}
