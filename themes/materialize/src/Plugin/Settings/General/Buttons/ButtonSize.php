<?php

namespace Drupal\materialize\Plugin\Setting\General\Buttons;

use Drupal\materialize\Plugin\Setting\SettingBase;

/**
 * The "button_size" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "button_size",
 *   type = "select",
 *   title = @Translation("Default button size"),
 *   defaultValue = "",
 *   description = @Translation("Defines the Materialize Buttons specific size"),
 *   empty_option = @Translation("Normal"),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "button" = @Translation("Buttons"),
 *   },
 *   options = {
 *     "btn-large" = @Translation("Large"),
 *   },
 * )
 */
class ButtonSize extends SettingBase {}
