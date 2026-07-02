<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MailSource;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLog extends Model
{
    protected $fillable = [
        'recipient',
        'subject',
        'mailable',
        'source_type',
        'source_user_id',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => MailSource::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    /**
     * Short class name of the Mailable (without namespace).
     *
     * @return Attribute<string|null, never>
     */
    protected function mailableShort(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->mailable !== null ? class_basename($this->mailable) : null,
        );
    }
}
