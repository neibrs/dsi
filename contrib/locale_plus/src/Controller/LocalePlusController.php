<?php

namespace Drupal\locale_plus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\locale\Locale;

class LocalePlusController extends ControllerBase {

  public function updateConfigTranslations() {
    $names = \Drupal::configFactory()->listAll();
    $number = Locale::config()->updateConfigTranslations($names, ['zh-hans']);
    \Drupal::messenger()->addMessage(t('@number of configuration updated.', ['@number' => $number]));

    return $this->redirect('<front>');
  }

}
