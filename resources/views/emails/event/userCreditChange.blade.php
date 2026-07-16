@php
use Illuminate\Support\Carbon;
use App\Enums\UserParamType;
use App\Shared\Helpers\AppHelper;

/** @var App\Models\User $user */
/** @var App\Models\UserCredit $userCredit */
@endphp

<x-mail::message>

## {{ __('mail/user-credit-change.body.heading') }}

{{ __('mail/user-credit-change.body.intro', ['name' => $user->name]) }}

{{ __('mail/user-credit-change.body.balance', [
    'balance' => $user->getParam(UserParamType::UserActualBalance),
    'date' => Carbon::now()->format(AppHelper::DATE_TIME_FORMAT),
]) }}

@component('mail::divider')
## {{ __('mail/user-credit-change.body.last_transaction_heading') }}

- {{ __('mail/user-credit-change.body.amount', ['amount' => $userCredit->amount]) }}
- {{ __('mail/user-credit-change.body.transaction_date', ['date' => Carbon::parse($userCredit->created_at)->format(AppHelper::DATE_TIME_FORMAT)]) }}
- {{ __('mail/user-credit-change.body.transaction_id', ['id' => $userCredit->id]) }}
- {{ __('mail/user-credit-change.body.bank_transaction_id', ['id' => $userCredit->bank_transaction_id]) }}

@endcomponent

{{ __('mail/user-credit-change.body.contact', ['email' => config('site-config.club.technical_email')]) }}

{{ __('mail/user-credit-change.body.signoff', ['club' => config('site-config.club.abbr')]) }}

</x-mail::message>
