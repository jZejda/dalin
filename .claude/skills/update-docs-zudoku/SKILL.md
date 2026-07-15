---
name: update-docs-zudoku
description: Updates Zudoku documentation for the DaLin project based on recent code changes. Use after commits or new features. Replaces the VitePress-based update-docs skill.
version: 1.0.0
context: fork
agent: docs-redactor-zudoku
argument-hint: "[number of commits or instruction]"
---

# Update Docs Skill (Zudoku)

Analyze recent changes in the DaLin project and propose documentation updates for the Zudoku docs site.

$ARGUMENTS

Follow the standard workflow:

1. **Find changes** using `git log` and `git diff` in the Laravel project root (`git rev-parse --show-toplevel`)
2. **Categorize** what belongs in user docs (`pages/napoveda/`) vs. technical docs (`pages/develop/`, `pages/install/`) and the changelog (`pages/changelog/index.mdx`)
3. **Sync the OpenAPI spec** — compare `storage/app/scribe/openapi.yaml` (app repo) with `apis/openapi.yaml` (docs repo) using `diff -q`; if they differ, copy the app version over. If the analyzed changes touched API code (`routes/api.php`, `app/Http/Controllers/Api/`, API resources), propose regenerating first with `vendor/bin/sail artisan scribe:generate`
4. **Read relevant existing documentation** in the Zudoku repository (`$DALIN_DOCS_ZUDOKU_PATH`, or `/home/bobik/projects/zudoku/dalin-docs-zudoku/` by default)
5. **Propose concrete changes** (new pages, edits, navigation entries in `zudoku.config.tsx`, changelog entries)
6. **Wait for confirmation** before writing — unless the user explicitly said "go ahead and write" or "update directly"
7. **After writing**, verify with `npm run build` in the docs repo (`nvm use 23.4.0` first)
