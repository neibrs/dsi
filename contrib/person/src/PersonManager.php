<?php

namespace Drupal\person;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\user\UserInterface;

class PersonManager implements PersonManagerInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Construct the PersonManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function currentPerson() {
    $user_storage = $this->entityTypeManager->getStorage('user');
    if ($user = $user_storage->load($this->currentUser->id())) {
      return $user->person->entity;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function currentPersonOrganizationByClassification($classification_id) {
    if ($person = $this->currentPerson()) {
      if ($classification_id == 'business_group') {
        return $person->business_group->entity;
      }
      else {
        return $person->getOrganizationByClassification($classification_id);
      }
    }

    if ($classification_id == 'business_group') {
      if ($default_business_group_id = \Drupal::config('organization.settings')->get('default_business_group')) {
        return \Drupal::entityTypeManager()->getStorage('organization')
          ->load($default_business_group_id);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserByPerson($person) {
    if (empty($person)) {
      return NULL;
    }
    if (is_object($person)) {
      $person = $person->id();
    }

    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $ids = $user_storage->getQuery()
      ->condition('person', $person)
      ->execute();
    if ($id = reset($ids)) {
      return $user_storage->load($id);
    }
  }

  public function getUserByPersonName($name = NULL) {
    if (empty($name)) {
      return NULL;
    }

    // TODO, 会出现同名的例外情况
    $persons = $this->entityTypeManager->getStorage('person')->loadByProperties([
      'name' => $name,
    ]);

    return $this->getUserByPerson(reset($persons));
  }
  /**
   * {@inheritdoc}
   */
  public function currentPersonAccessibleOrganizationByClassification($classification_id) {
    /** @var \Drupal\organization\OrganizationStorageInterface $organization_storage */
    $organization_storage = \Drupal::entityTypeManager()->getStorage('organization');

    $user = \Drupal::currentUser();
    if ($user->hasPermission('bypass multiple organization access')) {
      $organization_ids = $organization_storage->getQuery()
        ->condition('classifications', $classification_id)
        ->condition('status', TRUE)
        ->execute();
      return $organization_storage->loadMultiple($organization_ids);
    }

    $organization = $this->currentPersonOrganizationByClassification($classification_id);
    $organizations[$organization->id()] = $organization;
    if ($organization_children = $organization_storage->loadChildrenByClassification($organization->id(), $classification_id)) {
      $organizations += $organization_children;
    }
    return $organizations;
  }

  /**
   * {@inheritdoc}
   */
  public function personMultipleOrganizations($classification_id, $person_id = NULL) {
    /** @var \Drupal\organization\OrganizationStorageInterface $organization_storage */
    $organization_storage = \Drupal::entityTypeManager()->getStorage('organization');

    if (!$person_id) {
      // TODO add currentPersonOrganizationByClassification() parameter person_id
      $person_id = $this->currentPerson()->id();
    }
    $organization = $this->currentPersonOrganizationByClassification($classification_id);
    $organizations[$organization->id()] = $organization;
    if ($organization_parents = $organization_storage->loadParentsByClassification($organization->getParent()->id(), $classification_id)) {
      $organizations += $organization_parents;
    }
    return $organizations;
  }

  /**
   * {@inheritdoc}
   */
  public function getPersonByUser($user = NULL) {
    if (!$user) {
      $user = $this->currentUser->id();
    }
    if (is_integer($user) || is_string($user)) {
      $user = $this->entityTypeManager->getStorage('user')->load($user);
    }

    return $user->get('person')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getPersonTypes($system_type) {
    $query = $this->entityTypeManager->getStorage('person_type')->getQuery();
    $query->condition($query->orConditionGroup()
      ->condition('id', $system_type)
      ->condition('system_type', $system_type)
    );

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function personTypeIsEmployee($person_type) {
    if (is_object($person_type)) {
      $person_type = $person_type->id();
    }

    $employee_types = $this->getPersonTypes('employee');
    return in_array($person_type, $employee_types);
  }

  /**
   * {@inheritdoc}
   */
  public function parseResume($file) {
    if (is_numeric($file)) {
      $file = $this->entityTypeManager->getStorage('file')->load($file);
    }
    /** @var \Drupal\file\FileInterface $file */
    $cv_file = \Drupal::service('file_system')->realpath($file->getFileUri());
    $secret_key = \Drupal::config('person.settings')->get('resume_parsing_api_key');

    $cv_url = "https://api.youyun.com/v1/resume";
    if (class_exists('\CURLFile')) {
      $file = new \CURLFile($cv_file);
    } else {
      $file = "@{$cv_file}";
    }
    $data = [
      'secret_key' => $secret_key,
      'resume' => $file,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, $cv_url);
    $result = curl_exec($ch);
    if (!$result) {
      $result = curl_error($ch);
    }
    curl_close($ch);

    return $result;
  }
}
