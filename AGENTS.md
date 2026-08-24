\# Effe Website – Agent Instructions



\## Project



This repository is used to maintain the WordPress website for a small local house and property service business.



The production website is hosted at IONOS.



\## Important



The live WordPress installation is production.



Do not make destructive changes.

Do not modify WordPress core files.

Do not add credentials, passwords, API keys, SSH credentials, database credentials, or other secrets to the repository.



\## Theme



The relevant WordPress theme files are stored under:



theme/consted-roofing-flooring/



This is a child theme based on the Consted theme.



Prefer small, targeted changes.



Avoid unnecessary dependencies, plugins, frameworks, or architectural complexity.



\## WordPress



Content such as page text, menus, Contact Form 7 configuration and other database-backed WordPress settings may not exist in this repository.



Do not assume the repository represents the complete WordPress installation.



\## Workflow



Before modifying files:



1\. Inspect the relevant existing code.

2\. Identify the smallest reasonable change.

3\. Preserve existing WordPress/theme behavior.

4\. Avoid unrelated formatting or refactoring.

5\. Explain what was changed.



Do not deploy changes to production unless explicitly instructed.

## LocalWP content workflow

Local development uses the existing helper at `scripts/localwp-env.ps1`. It locates the LocalWP WordPress root, PHP, WP-CLI, and the active `php.ini`; do not duplicate that setup in other scripts.

Codex-managed page content lives in `content/pages/*.html`. Each file uses a small front matter block followed by the page HTML:

    ---
    slug: example-page
    title: Example page
    status: draft
    ---
    <p>Page HTML.</p>

From the repository root, inspect the planned changes safely with:

    powershell -ExecutionPolicy Bypass -File .\sync-content-local.ps1 -DryRun

The sync is dry-run by default. To write only to the configured LocalWP site, explicitly use:

    powershell -ExecutionPolicy Bypass -File .\sync-content-local.ps1 -Apply

The sync finds pages by exact slug, updates existing pages, and never deletes pages. It does not contact IONOS or production. An existing draft is only changed to `publish` when its managed file explicitly says `status: publish`. The existing `agb` page must remain draft, and `home` is intentionally not managed yet.

After a local synchronization, refresh rendered snapshots separately with:

    powershell -ExecutionPolicy Bypass -File .\refresh-seo-audit.ps1

Never deploy or modify production/IONOS unless the user explicitly requests it.

## Rendered site / SEO workflow

For tasks involving rendered HTML, SEO, metadata, headings, internal links, accessibility, or page output:

1. First run:
   powershell -ExecutionPolicy Bypass -File .\refresh-seo-audit.ps1

2. Inspect the generated HTML files under:
   seo-audit/

3. Base rendered-output conclusions on those snapshots rather than guessing from theme code alone.

4. If changes affect rendered output, run the refresh script again after the changes and re-check the relevant HTML.

5. Do not deploy to production automatically.

