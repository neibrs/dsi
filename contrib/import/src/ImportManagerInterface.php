<?php

namespace Drupal\import;

interface ImportManagerInterface {

  public function doMigrates($configurations);

}