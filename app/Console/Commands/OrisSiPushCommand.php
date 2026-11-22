<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\UserRaceProfile;
use App\Services\SiChips\OrisSiClient;
use App\Services\SiChips\OrisSiException;
use App\Services\SiChips\SiChipComparator;
use App\Services\SiChips\SiChipData;
use App\Services\SiChips\SiChipPusher;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('oris:si-push {reg_number : Registration number of the race profile} {--send : Really send the chips to ORIS (default is a dry run)}')]
#[Description('Show (and with --send perform) the editPerson call that sets the ORIS chips of a profile to the Dalin ones')]
class OrisSiPushCommand extends Command
{
    public function handle(SiChipPusher $pusher, SiChipComparator $comparator): int
    {
        $profile = UserRaceProfile::query()->with('chips')->where('reg_number', $this->argument('reg_number'))->first();

        if (! $profile instanceof UserRaceProfile) {
            $this->error('Race profile not found.');

            return self::FAILURE;
        }

        try {
            $payload = $pusher->preview($profile);
            $comparison = $pusher->compare($profile);
        } catch (OrisSiException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->line('Dalin chips: '.($this->labels($comparison->dalinChips) ?: '-'));
        $this->line('ORIS chips:  '.($this->labels($comparison->orisChips) ?: '-'));
        $this->line('Status:      '.$comparison->status->value);
        $this->newLine();
        $this->line('editPerson parameters (personal values hidden, they are copied unchanged from ORIS):');

        foreach ($payload as $name => $value) {
            $isChipField = str_starts_with($name, 'si');
            $this->line(sprintf('  %-10s %s', $name, $isChipField || $name === 'userid' ? var_export($value, true) : '<'.mb_strlen((string) $value).' chars>'));
        }

        if (! $this->option('send')) {
            $this->newLine();
            $this->info('Dry run - nothing was sent. Use --send to perform the call.');

            return self::SUCCESS;
        }

        try {
            $pusher->push($profile);
        } catch (OrisSiException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $after = $comparator->compare($profile->refresh(), app(OrisSiClient::class)->clubMembers(true));
        $this->info('Sent. ORIS chips now: '.($this->labels($after->orisChips) ?: '-').' ('.$after->status->value.')');

        return self::SUCCESS;
    }

    /**
     * @param  list<SiChipData>  $chips
     */
    private function labels(array $chips): string
    {
        return implode(', ', array_map(fn (SiChipData $chip): string => $chip->label(), $chips));
    }
}
