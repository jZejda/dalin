@php
    use App\Models\UserCredit;

    /**
     * @var UserCredit $bankTransaction
     */
@endphp

<div>
    @php
        $userCredits = UserCredit::query()
            ->where('bank_transaction_id' , '=', $bankTransaction->id)
            ->get();
    @endphp

    @if($userCredits !== null)
        @foreach($userCredits as $userCredit)
            <div class="mt-4 inline-flex items-center">
                <span class="size-2 inline-block bg-red-500 rounded-full me-2"></span>
                <span>Pozor tato transakce je již přidána uživateli <strong>{{ $userCredit->user->name }}</strong> VS: {{ $userCredit->user->payer_variable_symbol }}</span>
            </div>
            <div>
                <ul class="mt-4 ml-12 list-disc list-inside text-gray-700 dark:text-white">
                    <li>ID transkakce: {{$userCredit->id}}</li>
                    <li>Transakce ze dne: {{$userCredit->created_at}}</li>
                    <li>Hodnota připsané transakce: {{$userCredit->amount}},-</li>
                </ul>
            </div>
        @endforeach

    @endif
</div>
