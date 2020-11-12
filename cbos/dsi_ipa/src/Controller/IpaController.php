<?php

namespace Drupal\dsi_ipa\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\dsi_ipa\Entity\IpaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class IpaController.
 *
 *  Returns responses for IP Address routes.
 */
class IpaController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a IP Address revision.
   *
   * @param int $dsi_ipa_revision
   *   The IP Address revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($dsi_ipa_revision) {
    $dsi_ipa = $this->entityTypeManager()->getStorage('dsi_ipa')
      ->loadRevision($dsi_ipa_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('dsi_ipa');

    return $view_builder->view($dsi_ipa);
  }

  /**
   * Page title callback for a IP Address revision.
   *
   * @param int $dsi_ipa_revision
   *   The IP Address revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($dsi_ipa_revision) {
    $dsi_ipa = $this->entityTypeManager()->getStorage('dsi_ipa')
      ->loadRevision($dsi_ipa_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $dsi_ipa->label(),
      '%date' => $this->dateFormatter->format($dsi_ipa->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a IP Address.
   *
   * @param \Drupal\dsi_ipa\Entity\IpaInterface $dsi_ipa
   *   A IP Address object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(IpaInterface $dsi_ipa) {
    $account = $this->currentUser();
    $dsi_ipa_storage = $this->entityTypeManager()->getStorage('dsi_ipa');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $dsi_ipa->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all ip address revisions") || $account->hasPermission('administer ip address')));
    $delete_permission = (($account->hasPermission("delete all ip address revisions") || $account->hasPermission('administer ip address')));

    $rows = [];

    $vids = $dsi_ipa_storage->revisionIds($dsi_ipa);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\dsi_ipa\IpaInterface $revision */
      $revision = $dsi_ipa_storage->loadRevision($vid);
      $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

      // Use revision link to link to revisions that are not active.
      $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
      if ($vid != $dsi_ipa->getRevisionId()) {
        $link = $this->l($date, new Url('entity.dsi_ipa.revision', [
          'dsi_ipa' => $dsi_ipa->id(),
          'dsi_ipa_revision' => $vid,
        ]));
      }
      else {
        $link = $dsi_ipa->link($date);
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
            'url' => Url::fromRoute('entity.dsi_ipa.revision_revert', [
              'dsi_ipa' => $dsi_ipa->id(),
              'dsi_ipa_revision' => $vid,
            ]),
          ];
        }

        if ($delete_permission) {
          $links['delete'] = [
            'title' => $this->t('Delete'),
            'url' => Url::fromRoute('entity.dsi_ipa.revision_delete', [
              'dsi_ipa' => $dsi_ipa->id(),
              'dsi_ipa_revision' => $vid,
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

    $build['dsi_ipa_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
