# posts & get_post

Two tools for accessing club news articles.

---

## posts

Returns a paginated list of articles ordered by creation date.

**Class:** `App\Mcp\Tools\PostsTool`  
**Auth required:** No  
**Read-only:** Yes

### Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `from` | string | No | — | Filter articles created on or after this date (`Y-m-d`) |
| `to` | string | No | — | Filter articles created on or before this date (`Y-m-d`) |
| `per_page` | integer | No | 20 | Results per page (max 100) |

### Response

```json
{
  "data": [
    {
      "id": 7,
      "user_id": 3,
      "title": "Výsledky KP",
      "editorial": "Krátký perex článku...",
      "img_url": "/storage/posts/cover.jpg",
      "content_mode": 1,
      "private": 0,
      "created_at": "2025-04-15T10:00:00.000000Z",
      "updated_at": "2025-04-15T10:00:00.000000Z"
    }
  ],
  "per_page": 20,
  "next_cursor": null,
  "next_page_url": null
}
```

The `content` field is **not** included in the list response — use `get_post` to retrieve full article body.

### Notes

- `content_mode` indicates the format of the article body: `1` = HTML, `2` = plaintext, `3` = TipTap JSON.
- `private` reflects `App\Enums\PostStatus` — `0` = public, `1` = private/members-only.

---

## get_post

Returns the full detail of a single article, including its content body.

**Class:** `App\Mcp\Tools\GetPostTool`  
**Auth required:** No  
**Read-only:** Yes

### Parameters

| Name | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `post_id` | integer | **Yes** | — | ID of the article to retrieve |

### Response

```json
{
  "id": 7,
  "user_id": 3,
  "title": "Výsledky KP",
  "editorial": "Krátký perex článku...",
  "img_url": "/storage/posts/cover.jpg",
  "content": "<p>Celý text článku...</p>",
  "content_mode": 1,
  "private": 0,
  "created_at": "2025-04-15T10:00:00.000000Z",
  "updated_at": "2025-04-15T10:00:00.000000Z"
}
```

Returns an MCP error when `post_id` is missing or the article does not exist.

## Source

`app/Mcp/Tools/PostsTool.php`  
`app/Mcp/Tools/GetPostTool.php`
