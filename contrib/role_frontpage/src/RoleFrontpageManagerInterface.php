<?php

namespace Drupal\role_frontpage;

use Drupal\Core\Session\AccountInterface;

interface RoleFrontpageManagerInterface {

  public function getFrontpage(AccountInterface $account = NULL);

}
