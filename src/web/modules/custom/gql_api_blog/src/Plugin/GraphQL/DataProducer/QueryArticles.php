<?php

namespace Drupal\gql_api_blog\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\gql_api_blog\Wrappers\QueryConnection;
use GraphQL\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "query_articles",
 *   name = @Translation("Load articles"),
 *   description = @Translation("Loads a list of articles."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("articles connection")
 *   ),
 *   consumes = {
 *     "offset" = @ContextDefinition("integer",
 *       label = @Translation("Offset"),
 *       required = FALSE
 *     ),
 *     "limit" = @ContextDefinition("integer",
 *       label = @Translation("Limit"),
 *       required = FALSE
 *     ),
 *     "category" = @ContextDefinition("integer",
 *       label = @Translation("Category"),
 *       required = FALSE
 *     )
 *   }
 * )
 */
class Queryarticles extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  const MAX_LIMIT = 1000;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * articles constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $pluginId
   *   The plugin id.
   * @param mixed $pluginDefinition
   *   The plugin definition.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *
   * @codeCoverageIgnore
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityManager
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityManager = $entityManager;
  }

  /**
   * @param $offset
   * @param $limit
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $metadata
   *
   * @return \Drupal\gql_api_blog\Wrappers\QueryConnection
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function resolve($offset, $limit, $category, RefinableCacheableDependencyInterface $metadata) {
    if (!$limit > static::MAX_LIMIT) {
      throw new UserError(sprintf('Exceeded maximum query limit: %s.', static::MAX_LIMIT));
    }

    $storage = $this->entityManager->getStorage('node');
    $type = $storage->getEntityType();
    $query = $storage->getQuery()
      ->currentRevision()
      ->accessCheck();

    if ($category) {
      $query->condition('field_blog_category', $category);
    }

    $query->condition($type->getKey('bundle'), 'article');
    $query->condition('status', 1, '=');
    $query->range($offset, $limit);
    $query->sort('created', 'DESC');

    $metadata->addCacheTags($type->getListCacheTags());
    $metadata->addCacheContexts($type->getListCacheContexts());

    return new QueryConnection($query);
  }
}
