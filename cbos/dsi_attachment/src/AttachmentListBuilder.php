<?php

namespace Drupal\dsi_attachment;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Attachment entities.
 *
 * @ingroup dsi_attachment
 */
class AttachmentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Attachment ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_attachment\Entity\Attachment $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_attachment.edit_form',
      ['dsi_attachment' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
