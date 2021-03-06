<?php

namespace Drupal\person\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * @EntityReferenceSelection(
 *   id = "default:person_type",
 *   label = @Translation("Person type selection"),
 *   entity_types = {"person_type"},
 *   group = "default",
 *   weight = 1
 * )
 */
class PersonTypeSelection extends DefaultSelection {

  public function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator); // TODO: Change the autogenerated stub

    // 非管理人员不允许选择禁用的类型
    $account = \Drupal::currentUser();
    if ($account->hasPermission('administer persons')) {
      $query->condition('status', TRUE);
    }

    return $query;
  }

}