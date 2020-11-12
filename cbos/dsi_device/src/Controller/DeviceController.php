<?php

namespace Drupal\dsi_device\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\dsi_device\Entity\DeviceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DeviceController.
 *
 *  Returns responses for Device routes.
 */
class DeviceController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Device revision.
   *
   * @param int $dsi_device_revision
   *   The Device revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($dsi_device_revision) {
    $dsi_device = $this->entityTypeManager()->getStorage('dsi_device')
      ->loadRevision($dsi_device_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('dsi_device');

    return $view_builder->view($dsi_device);
  }

  /**
   * Page title callback for a Device revision.
   *
   * @param int $dsi_device_revision
   *   The Device revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($dsi_device_revision) {
    $dsi_device = $this->entityTypeManager()->getStorage('dsi_device')
      ->loadRevision($dsi_device_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $dsi_device->label(),
      '%date' => $this->dateFormatter->format($dsi_device->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Device.
   *
   * @param \Drupal\dsi_device\Entity\DeviceInterface $dsi_device
   *   A Device object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DeviceInterface $dsi_device) {
    $account = $this->currentUser();
    $dsi_device_storage = $this->entityTypeManager()->getStorage('dsi_device');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $dsi_device->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all device revisions") || $account->hasPermission('administer device')));
    $delete_permission = (($account->hasPermission("delete all device revisions") || $account->hasPermission('administer device')));

    $rows = [];

    $vids = $dsi_device_storage->revisionIds($dsi_device);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\dsi_device\DeviceInterface $revision */
      $revision = $dsi_device_storage->loadRevision($vid);
      $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

      // Use revision link to link to revisions that are not active.
      $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
      if ($vid != $dsi_device->getRevisionId()) {
        $link = $this->l($date, new Url('entity.dsi_device.revision', [
          'dsi_device' => $dsi_device->id(),
          'dsi_device_revision' => $vid,
        ]));
      }
      else {
        $link = $dsi_device->link($date);
      }

      $row = [];
      $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
      $row[] = $column;

      if ($latest_revision) {
        $row[] = [
          'data' => [
            '#prefix' => '<em>',
            '#markup' => $this->t('Current revision'),
            '#suffix' => '</em>',
          ],
        ];
        foreach ($row as &$current) {
          $current['class'] = ['revision-current'];
        }
        $latest_revision = FALSE;
      }
      else {
        $links = [];
        if ($revert_permission) {
          $links['revert'] = [
            'title' => $this->t('Revert'),
            'url' => Url::fromRoute('entity.dsi_device.revision_revert', [
              'dsi_device' => $dsi_device->id(),
              'dsi_device_revision' => $vid,
            ]),
          ];
        }

        if ($delete_permission) {
          $links['delete'] = [
            'title' => $this->t('Delete'),
            'url' => Url::fromRoute('entity.dsi_device.revision_delete', [
              'dsi_device' => $dsi_device->id(),
              'dsi_device_revision' => $vid,
            ]),
          ];
        }

        $row[] = [
          'data' => [
            '#type' => 'operations',
            '#links' => $links,
          ],
        ];
      }

      $rows[] = $row;
    }

    $build['dsi_device_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
