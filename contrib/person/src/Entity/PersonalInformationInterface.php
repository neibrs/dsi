<?php

namespace Drupal\person\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

interface PersonalInformationInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Gets the Person phone name.
   *
   * @return string
   *   Name of the Person phone.
   */
  public function getName();

  /**
   * Sets the Person phone name.
   *
   * @param string $name
   *   The Person phone name.
   *
   * @return \Drupal\person\Entity\PersonPhoneInterface
   *   The called Person phone entity.
   */
  public function setName($name);

  /**
   * Gets the Person phone creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Person phone.
   */
  public function getCreatedTime();

  /**
   * Sets the Person phone creation timestamp.
   *
   * @param int $timestamp
   *   The Person phone creation timestamp.
   *
   * @return \Drupal\person\Entity\PersonPhoneInterface
   *   The called Person phone entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * @return \Drupal\person\Entity\PersonInterface
   */
  public function getPerson();

}