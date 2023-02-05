<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/*
 * @property string $abbr
 * @property ?string $name
 * @property ?string $region_name
 * @property ?int $oris_id
 * @property ?int $oris_number
 *
 */
class Club extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'abbr',
        'name',
        'region_name',
        'oris_id',
        'oris_number',
    ];

}
