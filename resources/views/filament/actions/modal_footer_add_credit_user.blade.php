@php
    use App\Models\UserCredit;

    /**
     * @var UserCredit $record
     */
@endphp

<div>
    @php
        $userCredit = UserCredit::query()
            ->where('bank_transaction_id' , '=', $record->id)
            ->first();
    @endphp

    @if($userCredit !== null)
        <div class="inline-flex items-center">
            <span class="size-2 inline-block bg-red-500 rounded-full me-2"></span>
            <span>Pozor tato transakce je již přiřazená k uživateli  <strong>{{ $userCredit->user->name }}</strong></span>
        </div>
        <div>
            <ul class="mt-4 ml-12 list-disc list-inside text-gray-700 dark:text-white">
                <li>ID transkakce: {{$userCredit->id}}</li>
                <li>Transakce ze dne: {{$userCredit->created_at}}</li>
            </ul>
        </div>
    @endif
</div>
