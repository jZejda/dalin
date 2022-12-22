<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'oris_id',
        'short_name',
        'long_name',
    ];
}
