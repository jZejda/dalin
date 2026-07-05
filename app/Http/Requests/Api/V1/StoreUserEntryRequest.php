<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ownership of the race profile is checked in the controller.
        return $this->user() !== null;
    }

    /** @return array<string, list<string>|string> */
    public function rules(): array
    {
        return [
            'sport_event_id'       => ['required', 'integer', 'exists:sport_events,id'],
            'race_profile_id'      => ['required', 'integer', 'exists:user_race_profiles,id'],
            'class_id'             => ['nullable', 'integer', 'exists:sport_classes,id'],
            'relay_team_member_id' => ['nullable', 'integer', 'exists:relay_team_members,id'],
            'si'                   => ['nullable', 'integer'],
            'rent_si'              => ['nullable', 'boolean'],
            'note'                 => ['nullable', 'string', 'max:255'],
            'club_note'            => ['nullable', 'string', 'max:255'],
            'requested_start'      => ['nullable', 'string', 'max:255'],
            'entry_stages'         => ['nullable', 'array'],
            'entry_stages.*'       => ['string', 'regex:/^stage[0-9]+$/'],
        ];
    }
}
