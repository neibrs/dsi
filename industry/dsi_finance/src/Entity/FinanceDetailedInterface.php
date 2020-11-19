<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

interface FinanceDetailedInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface   {
  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the FinanceDetailed name.
   *
   * @return string
   *   Name of the FinanceDetailed.
   */
  public function getName();

  /**
   * Sets the FinanceDetailed name.
   *
   * @param string $name
   *   The FinanceDetailed name.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceDetailedInterface
   *   The called FinanceDetailed entity.
   */
  public function setName($name);

  /**
   * Gets the FinanceDetailed creation timestamp.
   *
   * @return int
   *   Creation timestamp of the FinanceDetailed.
   */
  public function getCreatedTime();

  /**
   * Sets the FinanceDetailed creation timestamp.
   *
   * @param int $timestamp
   *   The FinanceDetailed creation timestamp.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceDetailedInterface
   *   The called FinanceDetailed entity.
   */
  public function setCreatedTime($timestamp);
}