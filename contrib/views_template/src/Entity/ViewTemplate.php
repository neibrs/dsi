<?php

namespace Drupal\views_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the View template entity.
 *
 * @ConfigEntityType(
 *   id = "view_template",
 *   label = @Translation("View template"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\views_template\ViewsTemplateAccessControlHandler",
 *     "list_builder" = "Drupal\views_template\ViewTemplateListBuilder",
 *     "form" = {
 *       "add" = "Drupal\views_template\Form\ViewTemplateForm",
 *       "edit" = "Drupal\views_template\Form\ViewTemplateForm",
 *       "delete" = "Drupal\views_template\Form\ViewTemplateDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\views_template\ViewTemplateHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "view_template",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/view_template/{view_template}",
 *     "add-form" = "/view_template/add",
 *     "edit-form" = "/view_template/{view_template}/edit",
 *     "delete-form" = "/view_template/{view_template}/delete",
 *     "collection" = "/view_template"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "view_id",
 *     "filters",
 *     "fields",
 *     "is_public",
 *     "user_id",
 *   }
 * )
 */
class ViewTemplate extends ConfigEntityBase implements ViewTemplateInterface {

  /**
   * The View template ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The View template label.
   *
   * @var string
   */
  protected $label;
  
  protected $view_id;
  
  protected $filters;
  
  protected $fields;
  
  protected $is_public = FALSE;
  
  protected $user_id;
  
  /**
   * {@inheritdoc}
   */
  public function setViewId($view) {
    $this->view_id = is_object($view) ? $view->id() : $view;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getViewId() {
    return $this->view_id;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->filters;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return $this->fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setFields($fields) {
    $this->fields = $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    return $this->user_id;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setUserId($user) {
    $this->user_id = is_object($user) ? $user->id() : $user;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getIsPublic() {
    return $this->is_public;
  }
}
