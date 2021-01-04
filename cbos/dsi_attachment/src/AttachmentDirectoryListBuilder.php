<?php

namespace Drupal\dsi_attachment;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Attachment directory entities.
 *
 * @ingroup dsi_attachment
 */
class AttachmentDirectoryListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Attachment directory ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_attachment\Entity\AttachmentDirectory $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_attachment_directory.edit_form',
      ['dsi_attachment_directory' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
