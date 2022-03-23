<?php

namespace Drupal\gql_api_multisite\Plugin\GraphQL\Schema;

use Drupal\graphql\Plugin\GraphQL\Schema\ComposableSchema;

/**
 * @Schema(
 *   id = "composable",
 *   name = "Multisite schema",
 *   extensions = "composable",
 * )
 */
class MultisiteSchema extends ComposableSchema {

}
