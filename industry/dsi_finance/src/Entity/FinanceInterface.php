<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

interface FinanceInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {
  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Finance name.
   *
   * @return string
   *   Name of the Finance.
   */
  public function getName();

  /**
   * Sets the Finance name.
   *
   * @param string $name
   *   The Finance name.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceInterface
   *   The called Finance entity.
   */
  public function setName($name);

  /**
   * Gets the Finance creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Finance.
   */
  public function getCreatedTime();

  /**
   * Sets the Finance creation timestamp.
   *
   * @param int $timestamp
   *   The Finance creation timestamp.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceInterface
   *   The called Finance entity.
   */
  public function setCreatedTime($timestamp);
}