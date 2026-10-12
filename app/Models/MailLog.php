<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MailSource;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'recipient',
    'subject',
    'mailable',
    'source_type',
    'source_user_id',
])]
class MailLog extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    #[Override]
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
     * The user account the mail went to. The log only keeps the address, so it is matched by e-mail;
     * null for an address without an account.
     *
     * @return BelongsTo<User, $this>
     */
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient', 'email');
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
