<?php

namespace Drupal\organization;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Url;

/**
 * View builder handler for organizations.
 */
class OrganizationViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);

    $build['#attached']['library'][] = 'organization/view';

    if ($view_mode == 'full') {
      $section = \Drupal::service('organization.manager')
        ->getOrganizationSection()
        ->toRenderArray();
      $section['second']['organization'] = $build;
      return $section;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    if (empty($entities)) {
      return;
    }

    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      if ($display->getComponent('children')) {
        $build[$id]['children'] = [
          '#theme' => 'box',
          '#title' => t('Sub organizations'),
          '#icon' => 'fa fa-share-alt',
          '#tools' => [
            [
              '#type' => 'link',
              '#title' => t('Add'),
              '#url' => Url::fromRoute('entity.organization.add_form', [
                'parent' => $entity->id(),
                'organization_type' => 'department',
              ], [
                'query' => \Drupal::destination()->getAsArray(),
              ]),
            ]
          ],
        ];
        if ($view = views_embed_view('organization_children', 'default', $entity->id())) {
          $build[$id]['children']['#body'] = $view;
        }
        else {
          $build[$id]['children']['#body'] = $this->t('Views not found.');
        }
      }
    }
  }

}
