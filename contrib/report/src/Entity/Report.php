<?php

namespace Drupal\report\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Defines the Report entity.
 *
 * @ConfigEntityType(
 *   id = "report",
 *   label = @Translation("Report", context="Sheet"),
 *   label_collection = @Translation("Reports", context="Sheet"),
 *   handlers = {
 *     "access" = "Drupal\report\ReportAccessControlHandler",
 *     "view_builder" = "Drupal\report\ReportViewBuilder",
 *     "list_builder" = "Drupal\report\ReportListBuilder",
 *     "form" = {
 *       "add" = "Drupal\report\Form\ReportForm",
 *       "edit" = "Drupal\report\Form\ReportForm",
 *       "delete" = "Drupal\report\Form\ReportDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\report\ReportHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "report",
 *   admin_permission = "maintain reports",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/report/{report}",
 *     "add-page" = "/report/add",
 *     "add-form" = "/report/add/{plugin}",
 *     "edit-form" = "/report/{report}/edit",
 *     "delete-form" = "/report/{report}/delete",
 *     "collection" = "/report"
 *   }
 * )
 */
class Report extends ConfigEntityBase implements ReportInterface, EntityWithPluginCollectionInterface {

  /**
   * The Report ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Report label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Report category.
   *
   * @var string
   */
  protected $category;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected $plugin;

  /**
   * The plugin instance settings.
   *
   * @var array
   */
  protected $settings = [];

  /**
   * @var \Drupal\Core\Plugin\DefaultSingleLazyPluginCollection
   */
  protected $pluginCollection;

  /**
   * @return \Drupal\Core\Plugin\DefaultSingleLazyPluginCollection
   *   The report's plugin collection.
   */
  protected function getPluginCollection() {
    if (!$this->pluginCollection && $this->plugin) {
      $this->pluginCollection = new DefaultSingleLazyPluginCollection(\Drupal::service('plugin.manager.report'), $this->plugin, $this->get('settings'));
    }
    return $this->pluginCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'settings' => $this->getPluginCollection(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return $this->plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setPluginId($plugin_id) {
    $this->plugin = $plugin_id;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    return $this->getPluginCollection()->get($this->plugin);
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('report.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCategory() {
    return $this->category;
  }

  /**
   * {@inheritdoc}
   */
  public function setCategory($category) {
    $this->category = $category;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFiltersOverride() {
    $fields_override = \Drupal::service('tempstore.private')->get('report_filters_override')->get($this->id());
    if (!$fields_override) {
      $fields_override = $this->settings['filters'];
    }

    return $fields_override;
  }

  /**
   * {@inheritdoc}
   */
  public function setFiltersOverride($filters_override) {
    \Drupal::service('tempstore.private')->get('report_filters_override')->set($this->id(), $filters_override);

    return $this;
  }

}
