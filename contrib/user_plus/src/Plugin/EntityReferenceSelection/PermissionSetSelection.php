<?php

namespace Drupal\user_plus\Plugin\EntityReferenceSelection;

use Drupal\organization\Plugin\EntityReferenceSelection\MultipleOrganizationEntitySelection;

/**
 * @EntityReferenceSelection(
 *   id = "default:permission_set",
 *   label = @Translation("Permission set"),
 *   entity_types = {"permission_set"},
 *   group = "default",
 *   weight = 1
 * )
 */
class PermissionSetSelection extends MultipleOrganizationEntitySelection {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'match_fields' => ['name', 'pinyin', 'code'],
      ] + parent::defaultConfiguration();
  }

}