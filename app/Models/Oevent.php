<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Oevent
 */
class Oevent extends Model
{
    /**
     * Fillable fields
     *
     * @var array
     **/
    protected $fillable = [
        'title',
        'sport_id',
        'oris_id',
        'place',
        'url',
        'description',
        'first_date',
        'second_date',
        'third_date',
        'from_date',
        'to_date',
        'discipline_id',
        'clubs',
        'regions',
        'event_category',
        'is_canceled'

    ];

    protected $casts = [
        'clubs' => 'json',
        'regions' => 'json',
    ];


    /**
     * Return Legs for Oevent.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function legs()
    {
        return $this->hasMany(Oevent_leg::class);
    }
}
