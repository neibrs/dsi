<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\alert\Entity\Alert;
use Drupal\alert\Entity\AlertType;
use Drupal\Tests\BrowserTestBase;

abstract class AlertTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['alert'];

  /**
   * @var \Drupal\alert\Entity\AlertTypeInterface
   */
  protected $alertType;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->alertType = $this->createAlertType();
  }

  protected function createAlertType(array $settings = []) {
    $settings += [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $alert_type = AlertType::create($settings);
    $alert_type->save();

    return $alert_type;
  }

  /**
   * @param array $settings
   * @return \Drupal\alert\Entity\AlertInterface
   */
  protected function createAlert(array $settings = []) {
    $settings += [
      'name' => $this->randomMachineName(),
      'type' => $this->alertType->id(),
    ];
    $alert = Alert::create($settings);
    $alert->save();

    return $alert;
  }

}
