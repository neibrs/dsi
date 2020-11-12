<?php

namespace Drupal\person;

interface PersonManagerInterface {

  /**
   * Gets the current active user's person entity.
   *
   * @return \Drupal\person\Entity\PersonInterface
   *   The current person.
   */
  public function currentPerson();

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function currentPersonOrganizationByClassification($classification_id);

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function currentPersonAccessibleOrganizationByClassification($classification_id);

  /**
   * @param $classification_id
   *   Organization classification id.
   *
   * @param integer $person_id
   *
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function personMultipleOrganizations($classification_id, $person_id = NULL);

  /**
   * @param \Drupal\person\Entity\PersonInterface|integer|null $user
   *
   * @return \Drupal\person\Entity\PersonInterface
   */
  public function getPersonByUser($user = NULL);

  /**
   * @param $system_type
   *
   * @return string[]
   */
  public function getPersonTypes($system_type);

  /**
   * @return \Drupal\user\UserInterface
   */
  public function getUserByPerson($person);

  /**
   * @param $person_type
   *
   * @return boolean
   */
  public function personTypeIsEmployee($person_type);

  public function parseResume($file);

}
