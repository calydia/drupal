# Featured Category Post Implementation Plan

Date: 2026-08-02
Design: `2026-08-02-featured-category-post-design.md`

## Goal

Add a dedicated article checkbox and expose selected articles through the custom Drupal GraphQL API without changing existing article-query behavior.

## Work Sequence

1. Add boolean field storage and article-bundle field configuration.
2. Place the checkbox directly after the category widgets in the default article form.
3. Add `featuredCategoryPost: Boolean!` to the GraphQL `Article` type.
4. Add optional `featuredOnly: Boolean = false` to the `articles` query.
5. Map the new query argument into `QueryArticles` and apply its condition only when true.
6. Add a safe article-field resolver that returns false if the Drupal field is unavailable.
7. Import only the affected configuration into the running DDEV site.
8. Clear caches and verify schema introspection, normal queries, featured filtering, and combined category filtering.
9. Export configuration and ensure the repository remains synchronized.

## Verification

- PHP syntax checks for modified custom-module files.
- Drupal cache rebuild.
- GraphQL schema introspection for both new API additions.
- Query an unselected article and receive `false`.
- Query `featuredOnly: true` before selections and receive an empty list.
- Confirm the ordinary article query remains populated.
- Confirm configuration status contains no unexpected drift.
