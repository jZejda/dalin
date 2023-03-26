<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserCreditNote
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_credit_id
 * @property int $note_user_id
 * @property string $note
 * @property ?string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read User $user
 * @property-read User $userNoteMade
 * @property-read string $user_identification
 */
class UserCreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_credit_id',
        'note_user_id',
        'note',
        'status',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userNoteMade(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'note_user_id');
    }

    public function userCredit(): HasOne
    {
        return $this->hasOne(UserCredit::class, 'id', 'user_credit_id');
    }
}
