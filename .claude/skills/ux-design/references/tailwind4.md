# Tailwind CSS v4 — UX Reference for Custom Blade Components

Use **only** Tailwind v4 utility classes in custom Blade views, Livewire components, and public-facing pages.

---

## Key v4 Differences from v3

### Configuration moved to CSS
```css
/* resources/css/app.css — NOT tailwind.config.js */
@theme {
    --color-brand: #1a3a5c;
    --font-sans: 'Inter', sans-serif;
    --radius-card: 0.75rem;
}

@utility card {
    border-radius: var(--radius-card);
    background: white;
    box-shadow: var(--shadow-md);
}
```

### Container queries
```html
<div class="@container">
    <div class="@sm:grid-cols-2 @lg:grid-cols-3 grid grid-cols-1">...</div>
</div>
```

### Logical properties (RTL-safe)
```html
<!-- Use instead of pl-/pr-, ml-/mr- -->
<div class="ps-4 pe-6 ms-2 me-auto">...</div>
```

### New variants
```html
<!-- Negation -->
<button class="not-hover:opacity-75">...</button>

<!-- Ancestor state -->
<details>
    <div class="in-[details]:block hidden">...</div>
</details>

<!-- nth-child -->
<li class="nth-[3n]:bg-gray-100">...</li>
```

### Arbitrary values (unchanged from v3)
```html
<div class="grid-cols-[1fr_2fr] bg-[#1a3a5c] w-[calc(100%-2rem)]">...</div>
```

---

## Common UX Patterns

### Card
```html
<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
    ...
</div>
```

### Page header with action
```html
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Název stránky</h1>
        <p class="mt-1 text-sm text-gray-500">Popis sekce</p>
    </div>
    <div class="flex gap-3">
        <a href="#" class="btn-secondary">Zrušit</a>
        <button type="submit" class="btn-primary">Uložit</button>
    </div>
</div>
```

### Primary / Secondary / Danger buttons
```html
<!-- Primary -->
<button class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white
               hover:bg-brand/90 focus-visible:outline focus-visible:outline-2
               focus-visible:outline-offset-2 focus-visible:outline-brand">
    Primární akce
</button>

<!-- Secondary -->
<button class="rounded-lg ring-1 ring-gray-300 px-4 py-2 text-sm font-medium
               text-gray-700 hover:bg-gray-50">
    Sekundární
</button>

<!-- Danger -->
<button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white
               hover:bg-red-700">
    Smazat
</button>
```

### Responsive form grid
```html
<div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Jméno</label>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300
                                  shadow-sm focus:border-brand focus:ring-brand
                                  sm:text-sm">
    </div>
    ...
</div>
```

### Empty state
```html
<div class="flex flex-col items-center justify-center py-16 text-center">
    <svg class="h-12 w-12 text-gray-300" ...></svg>
    <h3 class="mt-4 text-sm font-semibold text-gray-900">Žádné záznamy</h3>
    <p class="mt-1 text-sm text-gray-500">Zatím zde nic není.</p>
    <div class="mt-6">
        <a href="#" class="btn-primary">Vytvořit první záznam</a>
    </div>
</div>
```

### Alert / feedback banner
```html
<!-- Success -->
<div class="rounded-lg bg-green-50 p-4 ring-1 ring-green-200">
    <p class="text-sm text-green-800">Uloženo úspěšně.</p>
</div>

<!-- Error -->
<div class="rounded-lg bg-red-50 p-4 ring-1 ring-red-200">
    <p class="text-sm text-red-800">Nastala chyba. Zkuste to znovu.</p>
</div>
```

---

## What to Avoid in v4

- `divide-*` utilities (removed) — use `border-t` / `border-b` on child elements instead
- `@apply` for composing utilities inside components — use direct class strings
- `plugin()` in JS config — use `@utility` in CSS instead
- v3 JIT arbitrary syntax variants that changed — test before assuming compatibility
