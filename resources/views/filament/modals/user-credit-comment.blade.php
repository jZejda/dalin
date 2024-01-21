@php
    use App\Models\UserCredit;

    /**
     * @var UserCredit $record
     */
@endphp

<div>
    @include('partials.backend.user-credit-notes', ['notes' => $record->userCreditNotes->all()])
</div>
