<?php

namespace Drupal\entity_log;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class EntityLogBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return $route_match->getRouteName() == 'entity.entity_log.entity_log';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();

    $links = [Link::createFromRoute($this->t('Home'), '<front>')];

    $entity_type_id = $route_match->getParameter('entity_type_id');
    $entity_id = $route_match->getParameter('entity_id');
    if ($entity = \Drupal::entityTypeManager()->getStorage($entity_type_id)->load($entity_id)) {
      $access = $entity->access('view', \Drupal::currentUser(), TRUE);
      if ($access->isAllowed()) {
        $breadcrumb->addCacheableDependency($access);
        $breadcrumb->addCacheContexts(['url']);
        $breadcrumb->addCacheTags([$entity_type_id . ':' . $entity_id]);
        $language_ch = new Language(['id' => 'zh-hans']);
        $links[] = $entity->toLink(NULL, 'canonical', ['language' => $language_ch]);
      }
    }
    $links[] = Link::createFromRoute($this->t('Change log'), '<none>');
    $breadcrumb->setLinks($links);


    return $breadcrumb;
  }

}
