<?php


namespace Drupal\Tests\dsi_project\Functional;


use Drupal\dsi_project\Entity\Project;
use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_project
 */
abstract class ProjectTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_project'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createProject(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Project::create($edit);
  }

}