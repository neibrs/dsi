<?php

namespace Drupal\font_awesome\Menu;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Menu\LocalActionInterface;
use Drupal\Core\Menu\LocalActionManager as LocalActionManagerBase;

/**
 */
class LocalActionManager extends LocalActionManagerBase {

  protected $iconMap = [
    'Add' => 'fa-plus',
    'Import' => 'fa-arrow-up',
    'Edit' => 'fa-edit',
  ];

  public function getTitle(LocalActionInterface $local_action) {
    $title = parent::getTitle($local_action);

    $definition = $local_action->getPluginDefinition();
    if (!isset($definition['icon'])) {
      $key = $definition['title'];
      if (is_object($key)) {
        $key = $key->getUntranslatedString();
      }
      if (isset($this->iconMap[$key])) {
        $icon = $this->iconMap[$key];
      }
    }
    else {
      $icon = $definition['icon'];
    }
    if (isset($icon) && !empty($icon)) {
      $title = new FormattableMarkup('<i class="fa @icon"></i> @title', [
        '@icon' => $icon,
        '@title' => $title,
      ]);
    }

    return $title;
  }

}
