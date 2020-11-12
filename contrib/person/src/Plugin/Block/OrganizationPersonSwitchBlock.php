<?php

namespace Drupal\person\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\person\Form\OrganizationPersonSwitchForm;

/**
 * Provides a 'OrganizationPersonSwitchBlock' block.
 *
 * @Block(
 *  id = "organization_person_switch_block",
 *  admin_label = @Translation("Organization person switch"),
 * )
 */
class OrganizationPersonSwitchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $route_match = \Drupal::routeMatch();
    /** @var \Drupal\person\Entity\PersonInterface $person */
    $person = $route_match->getParameter('person');
    if (!$person) {
      return [];
    }

    $build['switch'] = \Drupal::service('form_builder')->getForm(OrganizationPersonSwitchForm::class, $person);

    return $build;
  }

}
