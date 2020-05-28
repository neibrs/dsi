<?php

namespace Drupal\small_title\Plugin\Block;

use Drupal\Core\Block\TitleBlockPluginInterface;

interface SmallTitleBlockPluginInterface extends TitleBlockPluginInterface {

  public function setSmallTitle($title);

}
