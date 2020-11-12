<?php

namespace Drupal\person;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Provides a Person Type view builder.
 */
class PersonTypeViewBuilder extends EntityViewBuilder {

  public function buildMultiple(array $build_list) {
    $build_list = parent::buildMultiple($build_list);

    foreach ($build_list as $id => $build) {
      $entity = $build['#person_type'];
      $build_list[$id]['persons'] = views_embed_view('person_type_person', 'default', $entity->id());
    }
    return $build_list;
  }

}
