<?php

namespace Drupal\locale_plus\Command;

use Drupal\locale\Locale;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\Command;

/**
 * Class UpdateConfigTranslationsCommand.
 *
 * Drupal\Console\Annotations\DrupalCommand (
 *     extension="locale_plus",
 *     extensionType="module"
 * )
 */
class UpdateConfigTranslationsCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('locale_plus:update_config_translations')
      ->setDescription($this->trans('commands.locale_plus.update_config_translations.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('execute');

    $names = \Drupal::configFactory()->listAll();
    $number = Locale::config()->updateConfigTranslations($names, ['zh-hans']);

    $this->getIo()->info($this->trans('commands.locale_plus.update_config_translations.messages.success'));
  }

}
