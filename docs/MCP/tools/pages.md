# pages & get_page

Two tools for accessing CMS pages managed in the DaLin admin panel.

---

## pages

Returns a paginated list of CMS pages ordered by creation date.

**Class:** `App\Mcp\Tools\PagesTool`  
**Auth required:** No  
**Read-only:** Yes

### Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `from` | string | No | — | Filter pages created on or after this date (`Y-m-d`) |
| `to` | string | No | — | Filter pages created on or before this date (`Y-m-d`) |
| `status` | string | No | — | One of: `open`, `close`, `draft`, `archive` |
| `content_category_id` | integer | No | — | Filter by content category ID |
| `per_page` | integer | No | 20 | Results per page (max 100) |

### Response

```json
{
  "data": [
    {
      "id": 3,
      "user_id": 1,
      "content_category_id": 2,
      "title": "O klubu",
      "slug": "o-klubu",
      "content_format": 2,
      "picture_attachment": null,
      "status": "open",
      "weight": 10,
      "page_menu": true,
      "meta": null,
      "created_at": "2024-09-01T08:00:00.000000Z",
      "updated_at": "2025-01-10T12:00:00.000000Z"
    }
  ],
  "per_page": 20,
  "next_cursor": null,
  "next_page_url": null
}
```

The `content` field is **not** included in the list response — use `get_page` to retrieve the full page body.

### Notes

- `content_format` indicates the format: `1` = HTML, `2` = Markdown, `3` = TipTap JSON.
- `weight` controls display order in menus (lower = higher priority).
- `page_menu` indicates whether the page appears in navigation menus.

---

## get_page

Returns the full detail of a single CMS page, including its content body.

**Class:** `App\Mcp\Tools\GetPageTool`  
**Auth required:** No  
**Read-only:** Yes

### Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `page_id` | integer | **Yes** | — | ID of the page to retrieve |

### Response

```json
{
  "id": 3,
  "user_id": 1,
  "content_category_id": 2,
  "title": "O klubu",
  "slug": "o-klubu",
  "content": "## O našem klubu\n\nText stránky...",
  "content_format": 2,
  "picture_attachment": null,
  "status": "open",
  "weight": 10,
  "page_menu": true,
  "meta": null,
  "created_at": "2024-09-01T08:00:00.000000Z",
  "updated_at": "2025-01-10T12:00:00.000000Z"
}
```

Returns an MCP error when `page_id` is missing or the page does not exist.

### Notes

- TipTap JSON content (`content_format: 3`) is automatically decoded from its stored representation.

## Source

`app/Mcp/Tools/PagesTool.php`  
`app/Mcp/Tools/GetPageTool.php`
