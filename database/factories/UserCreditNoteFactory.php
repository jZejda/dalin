<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserCreditNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCreditNote>
 */
class UserCreditNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'user_credit_id' => null,
            'note_user_id'   => User::factory(),
            'note'           => $this->faker->sentence(),
            'internal'       => false,
            'params'         => null,
        ];
    }

    public function internal(): static
    {
        return $this->state(fn (): array => [
            'internal' => true,
        ]);
    }
}
