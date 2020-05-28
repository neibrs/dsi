<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityCreateAccessCheck as CoreEntityCreateAccessCheck;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Add entity_type_id placeholder support for access checker.
 */
class EntityCreateAccessCheck extends CoreEntityCreateAccessCheck {

  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    list($entity_type, $bundle) = explode(':', $route->getRequirement($this->requirementsKey) . ':');

    if (strpos($entity_type, '{') !== FALSE) {
      foreach ($route_match->getRawParameters()->all() as $name => $value) {
        $entity_type = str_replace('{' . $name . '}', $value, $entity_type);
      }
      // If we were unable to replace all placeholders, deny access.
      if (strpos($entity_type, '{') !== FALSE || !$this->entityManager->getDefinitions()[$entity_type]) {
        return AccessResult::neutral();
      }
    }

    // The bundle argument can contain request argument placeholders like
    // {name}, loop over the raw variables and attempt to replace them in the
    // bundle name. If a placeholder does not exist, it won't get replaced.
    if ($bundle && strpos($bundle, '{') !== FALSE) {
      foreach ($route_match->getRawParameters()->all() as $name => $value) {
        $bundle = str_replace('{' . $name . '}', $value, $bundle);
      }
      // If we were unable to replace all placeholders, deny access.
      if (strpos($bundle, '{') !== FALSE) {
        return AccessResult::neutral();
      }
    }
    return $this->entityManager->getAccessControlHandler($entity_type)->createAccess($bundle, $account, [], TRUE);
  }

}
