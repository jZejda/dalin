<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MailSource;
use App\Models\MailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MailLog>
 */
class MailLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recipient'      => $this->faker->safeEmail(),
            'subject'        => $this->faker->sentence(),
            'mailable'       => 'App\\Mail\\' . $this->faker->randomElement(['EntryConfirmation', 'CreditNotification', 'EventReminder']),
            'source_type'    => MailSource::System,
            'source_user_id' => null,
        ];
    }
}
