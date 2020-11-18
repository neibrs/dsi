<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

interface FinanceExpenditureInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface  {
  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the FinanceExpenditure name.
   *
   * @return string
   *   Name of the FinanceExpenditure.
   */
  public function getName();

  /**
   * Sets the FinanceExpenditure name.
   *
   * @param string $name
   *   The FinanceExpenditure name.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceExpenditureInterface
   *   The called Finance entity.
   */
  public function setName($name);

  /**
   * Gets the FinanceExpenditure creation timestamp.
   *
   * @return int
   *   Creation timestamp of the FinanceExpenditure.
   */
  public function getCreatedTime();

  /**
   * Sets the FinanceExpenditure creation timestamp.
   *
   * @param int $timestamp
   *   The Finance creation timestamp.
   *
   * @return \Drupal\dsi_finance\Entity\FinanceExpenditureInterface
   *   The called FinanceExpenditure entity.
   */
  public function setCreatedTime($timestamp);
}