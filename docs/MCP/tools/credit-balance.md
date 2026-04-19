# credit_balance

Returns the current credit balance of the authenticated user in CZK.

**Class:** `App\Mcp\Tools\CreditBalanceTool`  
**Auth required:** Yes — HTTP transport with a valid API key only  
**Read-only:** Yes

## Parameters

This tool takes no parameters.

## Response

```json
{
  "user_id": 12,
  "amount": 450.00,
  "currency": "CZK"
}
```

`amount` is the sum of all `UserCredit` records for the user. A positive value means the user has available credit; a negative value indicates debt.

## Notes

- This tool does **not** accept a `user_id` parameter. It only works via HTTP transport where the API key resolves the authenticated user.
- The balance is calculated directly from the `user_credits` table using `SUM(amount)`. It reflects the observer-maintained running total — see `App\Observers\UserCreditObserver`.
- `currency` is always `CZK` (constant `UserCredit::CURRENCY_CZK`).

## Source

`app/Mcp/Tools/CreditBalanceTool.php`
