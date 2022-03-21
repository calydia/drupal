<?php

namespace Drupal\gql_api_portfolio\Plugin\GraphQL\Schema;

use Drupal\graphql\Plugin\GraphQL\Schema\ComposableSchema;

/**
 * @Schema(
 *   id = "composable",
 *   name = "Portfolio schema",
 *   extensions = "composable",
 * )
 */
class PortfolioSchema extends ComposableSchema {

}
