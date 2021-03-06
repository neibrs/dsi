<?php

namespace Drupal\dsi_attachment\Controller;

use Drupal\Core\Controller\ControllerBase;

class AttachmentController extends ControllerBase {

  /**
   * Build document list.
   */
  public function getAttachments() {
    $build['#theme'] = 'attachment_entity';
    $build['#attached']['library'][] = 'dsi_attachment/attachment';

    return $build;
  }
}
