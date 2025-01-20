<?php

use App\Shared\Helpers\BankTransactionHelper;

test('compare variable symbols', function (string $variableSymbol, string $expectingSymbol) {
    expect(BankTransactionHelper::compareVariableSymbol($variableSymbol, $expectingSymbol))->toBeTrue();
})->with([
    ['8887805', '8887805'],
    ['0008887805', '8887805'],
    ['008887805', '8887805'],
]);

test('compare variable symbols strict validation', function () {
    expect(BankTransactionHelper::compareVariableSymbol('8887805', '8887805', true))->toBeTrue()
        ->and(BankTransactionHelper::compareVariableSymbol('0008887805', '8887805', true))->toBeFalse()
        ->and(BankTransactionHelper::compareVariableSymbol('8887805 KS:308', '8887805', true))->toBeFalse()
        ->and(BankTransactionHelper::compareVariableSymbol('8887805 KS:308', '8887805'))->toBeFalse();
});
