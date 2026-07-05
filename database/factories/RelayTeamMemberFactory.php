<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RelayTeam;
use App\Models\RelayTeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RelayTeamMember>
 */
class RelayTeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'relay_team_id'        => RelayTeam::factory(),
            // RelayTeam::created() pre-fills slots 1..slots_count, so take the next free one
            'slot'                 => fn (array $attributes): int => (int) RelayTeamMember::query()
                ->where('relay_team_id', $attributes['relay_team_id'])
                ->max('slot') + 1,
            'user_race_profile_id' => null,
            'user_entry_id'        => null,
        ];
    }
}
