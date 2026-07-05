<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\MailSource;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoMailLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');
        $users = User::where('email', 'like', '%@demo.cz')->get();

        if ($users->isEmpty()) {
            return;
        }

        $mails = [
            ['subject' => 'Potvrzení přihlášky na závod',       'mailable' => 'App\\Mail\\EntryConfirmation',   'source' => MailSource::User],
            ['subject' => 'Připsání kreditu na účet',           'mailable' => 'App\\Mail\\CreditNotification',  'source' => MailSource::System],
            ['subject' => 'Blíží se uzávěrka přihlášek',        'mailable' => 'App\\Mail\\EventReminder',       'source' => MailSource::Cron],
            ['subject' => 'Měsíční přehled kreditu',            'mailable' => 'App\\Mail\\CreditNotification',  'source' => MailSource::Cron],
            ['subject' => 'Potvrzení objednávky služby',        'mailable' => 'App\\Mail\\EntryConfirmation',   'source' => MailSource::User],
            ['subject' => 'Nová nabídka v klubovém tržišti',    'mailable' => 'App\\Mail\\EventReminder',       'source' => MailSource::System],
            ['subject' => 'Schválení žádosti o dopravu',        'mailable' => 'App\\Mail\\EntryConfirmation',   'source' => MailSource::User],
            ['subject' => 'Vyúčtování členských příspěvků',     'mailable' => 'App\\Mail\\CreditNotification',  'source' => MailSource::Cron],
        ];

        foreach ($mails as $index => $mail) {
            $user      = $users->random();
            $createdAt = Carbon::now()->subDays($index * 2 + $faker->numberBetween(0, 2));

            MailLog::factory()->create([
                'recipient'      => $user->email,
                'subject'        => $mail['subject'],
                'mailable'       => $mail['mailable'],
                'source_type'    => $mail['source'],
                'source_user_id' => $mail['source'] === MailSource::User ? $user->id : null,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);
        }
    }
}
