<?php

namespace Drupal\gql_api_multisite\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\GraphQL\Response\ResponseInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\gql_api_multisite\GraphQL\Response\ProjectResponse;
use Drupal\gql_api_multisite\GraphQL\Response\ArticleResponse;
use Drupal\gql_api_multisite\Wrappers\QueryConnection;

/**
 * @SchemaExtension(
 *   id = "composable_extension",
 *   name = "Composable multisite extension",
 *   description = "A simple extension that adds node related fields for the site.",
 *   schema = "composable"
 * )
 */
class MultisiteSchemaExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    // Project queries
    $registry->addFieldResolver('Query', 'project',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['portfolio_project']))
        ->map('id', $builder->fromArgument('id'))
    );

    $registry->addFieldResolver('Query', 'projectSlug',
      $builder->produce('query_slug')
        ->map('slug', $builder->fromArgument('slug'))
    );

    $registry->addFieldResolver('Query', 'projects',
      $builder->produce('query_projects')
        ->map('offset', $builder->fromArgument('offset'))
        ->map('limit', $builder->fromArgument('limit'))
    );

    // Course query
    $registry->addFieldResolver('Query', 'courses',
      $builder->produce('query_courses')
        ->map('offset', $builder->fromArgument('offset'))
        ->map('limit', $builder->fromArgument('limit'))
    );

    // Experience queries
    $registry->addFieldResolver('Query', 'experiences',
      $builder->produce('query_experiences')
        ->map('offset', $builder->fromArgument('offset'))
        ->map('limit', $builder->fromArgument('limit'))
    );

    $registry->addFieldResolver('Query', 'experienceSlug',
      $builder->produce('query_slug')
        ->map('slug', $builder->fromArgument('slug'))
    );

    // Portfolio Page query
    $registry->addFieldResolver('Query', 'portfolioPage',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['portfolio_page']))
        ->map('id', $builder->fromArgument('id'))
    );

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

    // Create project mutation.
    $registry->addFieldResolver('Mutation', 'createProject',
      $builder->produce('create_project')
        ->map('data', $builder->fromArgument('data'))
    );

    $registry->addFieldResolver('ProjectResponse', 'project',
      $builder->callback(function (ProjectResponse $response) {
        return $response->project();
      })
    );

    $registry->addFieldResolver('ProjectResponse', 'errors',
      $builder->callback(function (ProjectResponse $response) {
        return $response->getViolations();
      })
    );

    $registry->addFieldResolver('CourseResponse', 'course',
      $builder->callback(function (CourseResponse $response) {
        return $response->listing();
      })
    );

    $registry->addFieldResolver('CourseResponse', 'errors',
      $builder->callback(function (CourseResponse $response) {
        return $response->getViolations();
      })
    );

    $registry->addFieldResolver('ExperienceResponse', 'experience',
      $builder->callback(function (ExperienceResponse $response) {
        return $response->listing();
      })
    );

    $registry->addFieldResolver('ExperienceResponse', 'errors',
      $builder->callback(function (ExperienceResponse $response) {
        return $response->getViolations();
      })
    );

    // Project fields

    $registry->addFieldResolver('Project', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Project', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Project', 'published',
      $builder->produce('entity_published')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Project', 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value'))
    );

    $registry->addFieldResolver('Project', 'contentSummary',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.summary'))
    );

    $registry->addFieldResolver('Project', 'decorativeImageCredits',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_project_decorative_credit.value'))
    );

    $registry->addFieldResolver('Project', 'year',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_year.value'))
    );

    $registry->addFieldResolver('Project', 'metaDescription',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_meta_description.value'))
    );

    $registry->addFieldResolver('Project', 'slug',
      $builder->compose(
        $builder->produce('entity_url')
          ->map('entity', $builder->fromParent()),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Project', 'technologies',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_technologies.entity')),
        $builder->produce('entity_label')
          ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Project', 'roles',
      $builder->produce('entity_reference')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_roles_in_the_project'))
    );

    $registry->addFieldResolver('Role', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Role', 'name',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Project', 'technologies',
      $builder->produce('entity_reference')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_technologies'))
    );

    $registry->addFieldResolver('Technology', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Technology', 'name',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Technology', 'url',
      $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:taxonomy_term'))
      ->map('value', $builder->fromParent())
      ->map('path', $builder->fromValue('field_tech_url.value')),
    );

    $registry->addFieldResolver('Project', 'decorativeImage',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_decorative_image.entity')),
        $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:media'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_media_image.entity')),
        $builder->produce("image_url")
          ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Project', 'projectImages',
      $builder->produce('entity_reference')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_project_images'))
    );

    $registry->addFieldResolver('ProjectImage', 'url',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_media_image.entity')),
        $builder->produce("image_url")
          ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('ProjectImage', 'thumb',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_media_image.entity')),
          $builder->produce('image_derivative')
          ->map('entity', $builder->fromParent())
          ->map('style', $builder->fromValue('gatsby_gallery_image')),
          $builder->produce('image_style_url')
          ->map('derivative', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('ProjectImage', 'alt',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_media_image.alt')),
      )
    );

    $registry->addFieldResolver('ProjectImage', 'title',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_media_image.title')),
      )
    );

    $registry->addFieldResolver('Project', 'projectLink',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_link_to_project'))
    );

    $registry->addFieldResolver('Project', 'repoLinks',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_link_to_repository'))
    );

    $registry->addFieldResolver('Link', 'uri',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('field_item:link'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('uri'))
    );

    $registry->addFieldResolver('Link', 'title',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('field_item:link'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('title'))
    );


    // Course fields

    $registry->addFieldResolver('Course', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Course', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Course', 'year',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_year.value'))
    );

    $registry->addFieldResolver('Course', 'certificateLink',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_certificate'))
    );

    $registry->addFieldResolver('Course', 'date',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_date.value'))
    );

    $registry->addFieldResolver('Course', 'educator',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_educator.value'))
    );

    $registry->addFieldResolver('Course', 'relatedExperienceName',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_related_to_work_experience.entity')),
        $builder->produce('entity_label')
        ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Course', 'relatedExperienceSlug',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:node'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_related_to_work_experience.entity')),
        $builder->compose(
          $builder->produce('property_path')
            ->map('type', $builder->fromValue('entity:node'))
            ->map('value', $builder->fromParent())
            ->map('path', $builder->fromValue('field_slug.value'))
        )
      )
    );

    // Work experience fields

    $registry->addFieldResolver('Experience', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Experience', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Experience', 'published',
      $builder->produce('entity_published')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Experience', 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value'))
    );

    $registry->addFieldResolver('Experience', 'contentSummary',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.summary'))
    );

    $registry->addFieldResolver('Experience', 'slug',
      $builder->compose(
        $builder->produce('entity_url')
          ->map('entity', $builder->fromParent()),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Experience', 'endDate',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_end_date.value'))
    );

    $registry->addFieldResolver('Experience', 'startDate',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_start_date.value'))
    );

    $registry->addFieldResolver('Experience', 'metaDescription',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_meta_description.value'))
    );

    $registry->addFieldResolver('Experience', 'companyLink',
    $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:node'))
      ->map('value', $builder->fromParent())
      ->map('path', $builder->fromValue('field_company'))
  );


    // Portolio page fields

    $registry->addFieldResolver('PortfolioPage', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('PortfolioPage', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('PortfolioPage', 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value'))
    );

    $registry->addFieldResolver('PortfolioPage', 'metaDescription',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_meta_description.value'))
    );

    $registry->addFieldResolver('PortfolioPage', 'slug',
      $builder->compose(
        $builder->produce('entity_url')
          ->map('entity', $builder->fromParent()),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('ProjectConnection', 'total',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->total();
      })
    );

    $registry->addFieldResolver('ProjectConnection', 'items',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->items();
      })
    );

    $registry->addFieldResolver('CourseConnection', 'total',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->total();
      })
    );

    $registry->addFieldResolver('CourseConnection', 'items',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->items();
      })
    );

    $registry->addFieldResolver('ExperienceConnection', 'total',
    $builder->callback(function (QueryConnection $connection) {
      return $connection->total();
    })
  );

  $registry->addFieldResolver('ExperienceConnection', 'items',
    $builder->callback(function (QueryConnection $connection) {
      return $connection->items();
    })
  );

    // Article fields
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
            ->map('path', $builder->fromValue('field_writer_image.entity')),
          $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:media'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_media_image.entity')),
          $builder->produce("image_url")
            ->map('entity',$builder->fromParent())
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
      $builder->compose(
        $builder->produce('entity_url')
          ->map('entity', $builder->fromParent()),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
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
          ->map('path', $builder->fromValue('field_blog_main_image.entity')),
        $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:media'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_media_image.entity')),
        $builder->produce("image_url")
          ->map('entity',$builder->fromParent())
      )
    );

    $registry->addFieldResolver('Article', 'listingImage',
    $builder->compose(
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_blog_listing_image.entity')),
      $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:media'))
      ->map('value', $builder->fromParent())
      ->map('path', $builder->fromValue('field_media_image.entity')),
      $builder->produce("image_url")
        ->map('entity',$builder->fromParent())
    )
    );

    // Page fields
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
    $builder->compose(
      $builder->produce('entity_url')
        ->map('entity', $builder->fromParent()),
      $builder->produce('url_path')
        ->map('url', $builder->fromParent())
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
    if ($response instanceof ProjectResponse) {
      return 'ProjectResponse';
    }
    if ($response instanceof CourseResponse) {
      return 'CourseResponse';
    }
    if ($response instanceof ExperienceResponse) {
      return 'ExperienceResponse';
    }
    if ($response instanceof ArticleResponse) {
      return 'ArticleResponse';
    }

    throw new \Exception('Invalid response type.');
  }

}
