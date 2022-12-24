<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
        'long_name',
    ];
}
