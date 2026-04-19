# user_entries

Returns a paginated list of event entries for the authenticated (or specified) user.

**Class:** `App\Mcp\Tools\UserEntriesTool`  
**Auth required:** Yes  
**Read-only:** Yes

## Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `user_id` | integer | Conditional | — | Required when using stdio transport (no authenticated user) |
| `from` | string | No | today | Filter entries where the event date is on or after this date (`Y-m-d`) |
| `to` | string | No | — | Filter entries where the event date is on or before this date (`Y-m-d`) |
| `per_page` | integer | No | 20 | Results per page (max 100) |

`user_id` is ignored when the request is authenticated via HTTP API key.

## Response

```json
{
  "data": [
    {
      "id": 101,
      "sport_event": {
        "id": 42,
        "name": "KP Jihočeský kraj",
        "date": "2025-04-20"
      },
      "race_profile": {
        "id": 5,
        "name": "Jana Nováková"
      },
      "class_name": "D21",
      "requested_start": null,
      "rent_si": false,
      "entry_stages": null,
      "entry_status": "applied",
      "entry_created": "2025-04-10 18:45:00",
      "created_at": "2025-04-10T18:45:00.000000Z",
      "updated_at": "2025-04-10T18:45:00.000000Z"
    }
  ],
  "per_page": 20,
  "next_cursor": null,
  "next_page_url": null
}
```

Results are ordered by `entry_created` descending.

## Notes

- The default `from` value is today, so without date filters the tool returns only future entries.
- `entry_status` values are defined in `App\Enums\EntryStatus`.
- Entries are looked up across **all** race profiles belonging to the user.

## Source

`app/Mcp/Tools/UserEntriesTool.php`
