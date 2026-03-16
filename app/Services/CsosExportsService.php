<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Illuminate\Support\Str;

final class CsosExportsService
{
    /**
     * Generate a fixed-width text list of user entries for the given sport event.
     *
     * Each line represents one entry with fields placed into fixed column ranges:
     * reg_number (cols 0–8), class_name (cols 9–20), si (cols 21–32), full name
     * as "last_name first_name" (cols 33–57), and note (cols 58+). Entries without
     * a linked userRaceProfile are excluded; the note field uses the entry's note or
     * "L" when the note is empty.
     *
     * @param SportEvent $sportEvent The sport event to generate the entry list for.
     * @return string The assembled lines joined by newline characters.
     */
    public function generateEntryListText(SportEvent $sportEvent): string
    {
        // Load UserEntry records with userRaceProfile relationship
        $userEntries = UserEntry::query()
            ->where('sport_event_id', '=', $sportEvent->id)
            ->whereIn('entry_status', [EntryStatus::Create, EntryStatus::Edit])
            ->with(['userRaceProfile'])
            ->get();

        $lines = [];

        foreach ($userEntries as $userEntry) {
            // Skip entries without userRaceProfile
            if ($userEntry->userRaceProfile === null) {
                continue;
            }

            $profile = $userEntry->userRaceProfile;

            // Col 0-8: reg_number (max 8 chars, left-align)
            $regNumber = $this->truncateString($profile->reg_number ?? '', 8);
            $regNumber = mb_str_pad($regNumber, 8, ' ', STR_PAD_RIGHT);

            // Col 9-20: class_name (max 11 chars, left-align)
            $className = $this->truncateString(Str::trim($userEntry->class_name ?? ''), 11);
            $className = mb_str_pad($className, 11, ' ', STR_PAD_RIGHT);

            // Col 21-32: si (max 11 chars, right-align)
            $si = $userEntry->si !== null ? Str::trim((string) $userEntry->si) : '';
            $si = $this->truncateString($si, 11);
            $si = mb_str_pad($si, 11, ' ', STR_PAD_RIGHT);

            // Col 33-57: last_name + space + first_name (max 27 chars, left-align)
            $lastName = $profile->last_name ?? '';
            $firstName = $profile->first_name ?? '';
            $fullName = trim($lastName . ' ' . $firstName);
            $fullName = $this->truncateString($fullName, 27);
            $fullName = mb_str_pad($fullName, 27, ' ', STR_PAD_RIGHT);

            // Col 58+: "L" or note (if note is not null)
            $note = $userEntry->note !== null && $userEntry->note !== '' ? $userEntry->note : 'L';

            // Build the line
            $line = $regNumber . $className . $si . $fullName . $note;
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Truncate string to maximum length
     */
    private function truncateString(string $value, int $maxLength): string
    {
        if (mb_strlen($value) > $maxLength) {
            return mb_substr($value, 0, $maxLength);
        }

        return $value;
    }
}
