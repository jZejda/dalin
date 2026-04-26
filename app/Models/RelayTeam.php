<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $sport_event_id
 * @property int|null $sport_class_id
 * @property string $name
 * @property string $relay_type
 * @property int $slots_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RelayTeam extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (self $team): void {
            $team->syncMemberSlots();
        });

        static::updated(function (self $team): void {
            if ($team->wasChanged('slots_count')) {
                $team->syncMemberSlots();
            }
        });
    }

    /** @var list<string> */
    protected $fillable = [
        'sport_event_id',
        'sport_class_id',
        'name',
        'relay_type',
        'slots_count',
    ];

    public function sportEvent(): BelongsTo
    {
        return $this->belongsTo(SportEvent::class, 'sport_event_id', 'id');
    }

    public function sportClass(): BelongsTo
    {
        return $this->belongsTo(SportClass::class, 'sport_class_id', 'id');
    }

    /** @return HasMany<RelayTeamMember, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(RelayTeamMember::class, 'relay_team_id', 'id');
    }

    public function syncMemberSlots(): void
    {
        $expectedSlots = range(1, $this->slots_count);

        $existingSlots = $this->members()->pluck('slot')->all();

        foreach ($expectedSlots as $slot) {
            if (! in_array($slot, $existingSlots, true)) {
                RelayTeamMember::query()->create([
                    'relay_team_id' => $this->id,
                    'slot' => $slot,
                ]);
            }
        }

        $this->members()
            ->where('slot', '>', $this->slots_count)
            ->whereNull('user_entry_id')
            ->delete();
    }
}
