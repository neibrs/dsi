<?php

namespace Drupal\organization\Plugin\EntityReferenceSelection;

/**
 * @EntityReferenceSelection(
 *   id = "default:organization",
 *   label = @Translation("Organization selection"),
 *   entity_types = {"organization"},
 *   group = "default",
 *   weight = 1
 * )
 */
class OrganizationSelection extends MultipleOrganizationEntitySelection {

}
