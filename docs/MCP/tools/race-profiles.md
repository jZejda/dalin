# race_profiles

Returns the race profiles (competitor registrations) for the authenticated user.

**Class:** `App\Mcp\Tools\RaceProfilesTool`  
**Auth required:** Yes — HTTP transport with a valid API key only  
**Read-only:** Yes

## Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `include_inactive` | boolean | No | `false` | When `true`, also returns inactive profiles |

## Response

```json
{
  "data": [
    {
      "id": 5,
      "reg_number": "JBC0001",
      "first_name": "Jana",
      "last_name": "Nováková",
      "full_name": "Jana Nováková",
      "email": "jana@example.cz",
      "phone": "+420 777 123 456",
      "gender": "F",
      "active": true,
      "active_until": "2025-12-31",
      "created_at": "2023-06-01T10:00:00.000000Z",
      "updated_at": "2024-01-15T08:30:00.000000Z"
    }
  ]
}
```

Results are ordered: active profiles first, then alphabetically by `last_name`, `first_name`.

## Notes

- This tool does **not** accept a `user_id` parameter. It only works via HTTP transport where the API key resolves the authenticated user.
- `reg_number` is the ORIS registration number used for race entries.
- `active_until` is `null` when no expiry date is set.

## Source

`app/Mcp/Tools/RaceProfilesTool.php`
