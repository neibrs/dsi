<?php

namespace Drupal\dsi_attachment\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Attachment directory entities.
 */
class AttachmentDirectoryViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
