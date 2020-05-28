<?php

namespace Drupal\views_template\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'View template' block.
 *
 * @Block(
 *  id = "view_template_block",
 *  admin_label = @Translation("View template block"),
 * )
 */
class ViewTemplateBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('\Drupal\views_template\Form\ViewTemplateSwitchForm');
    return $form;
  }
  
}
