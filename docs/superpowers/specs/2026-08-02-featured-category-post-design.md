# Featured Category Post Design

Date: 2026-08-02
Status: Approved in conversation, pending review of written spec

## Goal

Allow editors to mark blog articles as recommended starting points for their primary categories and expose those selections efficiently through the custom GraphQL API. Multiple selected articles in one category must remain safe; the Astro frontend will choose the newest.

## Content Model

Add an optional boolean field to the `article` node bundle:

- Machine name: `field_featured_category_post`
- Editor label: `Featured category post`
- Default value: unchecked
- Required: no
- Translatable: no
- Cardinality: one
- Help text: `Recommend this article as a starting point for its primary blog category. If several articles are selected in one category, the newest is used.`

Place the checkbox near the primary and secondary blog-category fields in the default article form display. The article's existing primary category determines which frontend category receives the recommendation.

## GraphQL Contract

Extend `Article` with:

```graphql
featuredCategoryPost: Boolean!
```

The resolver reads `field_featured_category_post.value`, casts the stored value to a boolean, and returns `false` if the field is temporarily unavailable. The non-null contract gives frontend consumers a stable boolean.

Extend the existing `articles` query with:

```graphql
featuredOnly: Boolean = false
```

When `featuredOnly` is true, `QueryArticles` adds a condition requiring `field_featured_category_post` to equal `1`. When false or omitted, query behavior remains unchanged. The argument remains compatible with the existing category argument.

Only published, current article revisions continue to be returned, sorted newest first.

## Drupal Configuration

Commit configuration for boolean field storage, the field instance on the article bundle, and the updated default article form display. The field does not need to appear in public Drupal view displays because Astro consumes it through GraphQL.

## Frontend Integration Boundary

Drupal returns all marked articles and does not enforce one selection per category. Astro groups selections by primary category, chooses the newest selection, warns in server/build logs about duplicates, and retains its local-slug and newest-post fallbacks.

## Failure Handling

- Missing field during a staged code/config deployment resolves to `false` in GraphQL.
- No selected articles returns an empty connection.
- Multiple selected articles are returned normally in newest-first order.
- Omitting `featuredOnly` preserves current query behavior.
- Category and featured filtering can be combined.

## Verification

- Import the added configuration into the local DDEV site.
- Confirm the checkbox appears near category fields on article forms.
- Confirm an unselected article returns `featuredCategoryPost: false`.
- Select at least two articles in one category and one in another category.
- Confirm `featuredOnly: true` returns all selected published articles newest first.
- Confirm combined category and featured filtering returns only selections in that category.
- Confirm the normal articles query remains unchanged when the argument is omitted.
- Run PHP syntax and available project coding checks.
- Export configuration and confirm it matches the committed YAML.

## Non-Goals

- Enforcing exactly one selection per category in Drupal.
- Showing editorial warnings to anonymous visitors.
- Automatically unchecking older articles.
- Changing the article primary-category model.
- Implementing Astro consumption in the Drupal change itself.
