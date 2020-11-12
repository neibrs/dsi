<?php

namespace Drupal\report\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

class ReportRowsAccessCheck implements AccessInterface {
  
  public function access(AccountInterface $account, $report = NULL) {
    if (!is_object($report)) {
      $report = \Drupal::entityTypeManager()->getStorage('report')->load($report);
    }
    
    if ($report && $report->getPluginId() == 'cross_table') {
      return AccessResult::allowed();
    }
    
    return AccessResult::neutral();
  }
}