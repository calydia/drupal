<?php

namespace Drupal\gql_api_blog\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\GraphQL\Response\ResponseInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\gql_api_blog\GraphQL\Response\ArticleResponse;
use Drupal\gql_api_blog\Wrappers\QueryConnection;

/**
 * @SchemaExtension(
 *   id = "composable_extension_blog",
 *   name = "Composable Blog extension",
 *   description = "A simple extension that adds node related fields for the blog.",
 *   schema = "composable"
 * )
 */
class BlogSchemaExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    $registry->addFieldResolver('Query', 'article',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['article']))
        ->map('id', $builder->fromArgument('id'))
    );

    $registry->addFieldResolver('Query', 'articleSlug',
      $builder->produce('query_slug')
        ->map('slug', $builder->fromArgument('slug'))
    );

    $registry->addFieldResolver('Query', 'articles',
      $builder->produce('query_articles')
        ->map('offset', $builder->fromArgument('offset'))
        ->map('limit', $builder->fromArgument('limit'))
        ->map('category', $builder->fromArgument('category'))
    );

    $registry->addFieldResolver('Query', 'page',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['page']))
        ->map('id', $builder->fromArgument('id'))
    );


    // Create article mutation.
    $registry->addFieldResolver('Mutation', 'createArticle',
      $builder->produce('create_article')
        ->map('data', $builder->fromArgument('data'))
    );

    $registry->addFieldResolver('ArticleResponse', 'article',
      $builder->callback(function (ArticleResponse $response) {
        return $response->article();
      })
    );

    $registry->addFieldResolver('ArticleResponse', 'errors',
      $builder->callback(function (ArticleResponse $response) {
        return $response->getViolations();
      })
    );

    $registry->addFieldResolver('Article', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Article', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Article', 'date',
      $builder->produce('entity_created')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Article', 'published',
      $builder->produce('entity_published')
        ->map('entity', $builder->fromParent()
      )
    );

    $registry->addFieldResolver('Article', 'authorName',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_writer.entity')
        ),
        $builder->produce('entity_label')
          ->map('entity',$builder->fromParent()
        )
      )
    );

    $registry->addFieldResolver('Article', 'authorImage',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_writer.entity')
        ),
        $builder->compose(
          $builder->produce('property_path')
            ->map('type', $builder->fromValue('entity:node'))
            ->map('value', $builder->fromParent())
            ->map('path', $builder->fromValue('field_image.entity')
          ),
          $builder->produce("image_url")
            ->map('entity',$builder->fromParent()
          )
        )
      )
    );

    $registry->addFieldResolver('Article', 'authorContent',
    $builder->compose(
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_writer.entity')
      ),
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('body.value')
        )
      )
    )
  );

    $registry->addFieldResolver('Article', 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value')
      )
    );

    $registry->addFieldResolver('Article', 'metaDescription',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_meta_description.value')
      )
    );

    $registry->addFieldResolver('Article', 'slug',
      $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:node'))
      ->map('value', $builder->fromParent())
      ->map('path', $builder->fromValue('field_slug.value'))
    );


    $registry->addFieldResolver('Article', 'imageCredits',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_image_credits.value')
      )
    );

    $registry->addFieldResolver('Article', 'category',
    $builder->compose(
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_blog_category.entity')
      ),
      $builder->produce('entity_label')
        ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Article', 'mainImage',
      $builder->compose(
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_main_image.entity')
      ),
      $builder->produce("image_url")
        ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Article', 'listingImage',
      $builder->compose(
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_listing_image.entity')
      ),
      $builder->produce("image_url")
        ->map('entity',$builder->fromParent())
      )
    );


    $registry->addFieldResolver('Page', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Page', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Page', 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value')
      )
    );

    $registry->addFieldResolver('Page', 'metaDescription',
    $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:node'))
      ->map('value', $builder->fromParent())
      ->map('path', $builder->fromValue('field_meta_description.value')
    )
  );

    $registry->addFieldResolver('Page', 'slug',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_slug.value')
      )
    );

    $registry->addFieldResolver('ArticleConnection', 'total',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->total();
      })
    );

    $registry->addFieldResolver('ArticleConnection', 'items',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->items();
      })
    );


    // Response type resolver.
    $registry->addTypeResolver('Response', [
      __CLASS__,
      'resolveResponse',
    ]);
  }

  /**
   * Resolves the response type.
   *
   * @param \Drupal\graphql\GraphQL\Response\ResponseInterface $response
   *   Response object.
   *
   * @return string
   *   Response type.
   *
   * @throws \Exception
   *   Invalid response type.
   */
  public static function resolveResponse(ResponseInterface $response): string {
    // Resolve content response.
    if ($response instanceof ArticleResponse) {
      return 'ArticleResponse';
    }

    throw new \Exception('Invalid response type.');
  }

}
