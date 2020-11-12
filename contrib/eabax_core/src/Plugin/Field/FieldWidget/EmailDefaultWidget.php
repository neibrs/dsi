<?php

namespace Drupal\eabax_core\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\EmailDefaultWidget as EmailDefaultWidgetBase;

/**
 * Decrease the default size.
 */
class EmailDefaultWidget extends EmailDefaultWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings =  parent::defaultSettings();

    $settings['size'] = 40;

    return $settings;
  }

}
