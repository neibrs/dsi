<?php

namespace Drupal\import;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class ImportSelectSheetBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  
  use StringTranslationTrait;
  
  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return $route_match->getRouteName() == 'import.entity_import.select_sheet';
  }
  
  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
  
    $links = [Link::createFromRoute($this->t('Home'), '<front>')];
  
    $migration_id = $route_match->getParameter('migration_id');
    $options = \Drupal::service('tempstore.private')->get('import_entity.import_form.options')->get($migration_id);
    if (isset($options['configuration']['source']['entity_type_id'])) {
      $entity_type = \Drupal::entityTypeManager()->getDefinition($options['configuration']['source']['entity_type_id']);
      $links[] = Link::createFromRoute($entity_type->getLabel(), 'entity.' . $entity_type->id() . '.collection');
  
      $breadcrumb->addCacheContexts(['url']);
      $breadcrumb->addCacheTags([$migration_id . ':' . $migration_id]);
    }
    $links[] = Link::createFromRoute($this->t('Select Sheet For Import'), '<none>');
    $breadcrumb->setLinks($links);
    
    return $breadcrumb;
  }
}