# sport_events

Returns a paginated list of sport events (races, trainings, camps, etc.).

**Class:** `App\Mcp\Tools\SportEventsTool`  
**Auth required:** No  
**Read-only:** Yes

## Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `from` | string | No | — | Filter events on or after this date (`Y-m-d`) |
| `to` | string | No | — | Filter events on or before this date (`Y-m-d`) |
| `event_type` | string | No | — | One of: `race`, `training`, `trainingCamp`, `other` |
| `class_definition_id` | integer | No | — | Only return events that have this category/class |
| `per_page` | integer | No | 20 | Results per page (max 100) |

## Response

```json
{
  "data": [
    {
      "id": 42,
      "name": "KP Jihočeský kraj",
      "date": "2025-04-20",
      "entry_date": "2025-04-13 23:59:59",
      "event_type": {
        "value": "race",
        "label": "race"
      },
      "categories": [
        { "id": 3, "name": "H21" },
        { "id": 7, "name": "D21" }
      ],
      "created_at": "2025-01-10T09:00:00.000000Z",
      "updated_at": "2025-03-01T14:22:00.000000Z"
    }
  ],
  "per_page": 20,
  "next_cursor": null,
  "next_page_url": null
}
```

Results are ordered by `date` descending (most recent first).

## Notes

- `entry_date` corresponds to the first entry deadline (`entry_date_1` in the database).
- `event_type` values are defined in `App\Enums\SportEventType`. Invalid values are silently ignored (no filtering applied).
- `categories` lists the unique class definitions attached to the event via `SportClass` pivot records.

## Source

`app/Mcp/Tools/SportEventsTool.php`
