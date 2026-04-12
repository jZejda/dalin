---
name: update-docs
description: Updates VitePress documentation for the DaLin project based on recent code changes. Use after commits or new features.
version: 1.0.0
context: fork
agent: docs-redactor
argument-hint: "[number of commits or instruction]"
---

# Update Docs Skill

Analyze recent changes in the DaLin project and propose documentation updates.

$ARGUMENTS

Follow the standard workflow:

1. **Find changes** using `git log` and `git diff` in the Laravel project root (`git rev-parse --show-toplevel`)
2. **Categorize** what belongs in user docs (`docs/napoveda/`) vs. technical docs (`docs/develop/`, `docs/install/`) and the changelog
3. **Read relevant existing documentation** in the VitePress repository (`$DALIN_DOCS_PATH`, or `<parent-of-laravel-root>/vitepress/dalin-docs/` by default)
4. **Propose concrete changes** (new pages, edits, changelog entries)
5. **Wait for confirmation** before writing — unless the user explicitly said "go ahead and write" or "update directly"
