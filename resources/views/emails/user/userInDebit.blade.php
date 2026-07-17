<?php
    use Illuminate\Support\Carbon;
    /** @var array{array{fullName: string, email: string, debit: float|int}} $usersData */
?>

<x-mail::message>

## {{ __('mail/users-in-debit.body.heading') }}

{{ __('mail/users-in-debit.body.intro', [
    'club' => Config::get('site-config.club.abbr'),
    'date' => Carbon::now()->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT),
]) }}

{{ __('mail/users-in-debit.body.check_note') }}

@component('mail::table')
    | {{ __('mail/users-in-debit.body.table_header_user') }} | {{ __('mail/users-in-debit.body.table_header_balance') }} |
    | :----------------- |:------------- |
    @foreach ($usersData as $user)
        | {{$user['fullName'] }} ({{$user['email']}})  | {{ __('mail/users-in-debit.body.table_row_debit', ['debit' => $user['debit']]) }} |
    @endforeach
@endcomponent

</x-mail::message>
