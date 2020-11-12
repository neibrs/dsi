<?php

namespace Drupal\dsi_ipa\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a IP Address revision.
 *
 * @ingroup dsi_ipa
 */
class IpaRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The IP Address revision.
   *
   * @var \Drupal\dsi_ipa\Entity\IpaInterface
   */
  protected $revision;

  /**
   * The IP Address storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $ipaStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->ipaStorage = $container->get('entity_type.manager')->getStorage('dsi_ipa');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dsi_ipa_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.dsi_ipa.version_history', ['dsi_ipa' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $dsi_ipa_revision = NULL) {
    $this->revision = $this->IpaStorage->loadRevision($dsi_ipa_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->IpaStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('IP Address: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of IP Address %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.dsi_ipa.canonical',
       ['dsi_ipa' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {dsi_ipa_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.dsi_ipa.version_history',
         ['dsi_ipa' => $this->revision->id()]
      );
    }
  }

}
