<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Leg
 */
class Oevent_leg extends Model
{
    /**
     * Fillable fields
     *
     * @var array
     **/
    protected $fillable = [
        'oevent_id',
        'oris_id',
        'discipline_id',
        'title',
        'leg_datetime',
        'lat',
        'lon',
        'description'
    ];

    public function oevent()
    {
        return $this->hasOne(\App\Oevent::class, 'id', 'oevent_id');
    }

}
