<?php

namespace Drupal\locale_plus;

class LocalePlusManager implements LocalePlusManagerInterface {

  /**
   * {@inheritdoc}
   */
  function localeUpdate($options = ['langcodes' => 'zh-hans, en']) {
    $module_handler = \Drupal::moduleHandler();
    $module_handler->loadInclude('locale', 'fetch.inc');
    $module_handler->loadInclude('locale', 'bulk.inc');

    $langcodes = [];
    foreach (locale_translation_get_status() as $project_id => $project) {
      foreach ($project as $langcode => $project_info) {
        if (!empty($project_info->type) && !in_array($langcode, $langcodes)) {
          $langcodes[] = $langcode;
        }
      }
    }

    if ($passed_langcodes = $options['langcodes']) {
      $langcodes = array_intersect($langcodes, explode(',', $passed_langcodes));
      // @todo Not selecting any language code in the user interface results in
      //   all translations being updated, so we mimick that behavior here.
    }

    // Deduplicate the list of langcodes since each project may have added the
    // same language several times.
    $langcodes = array_unique($langcodes);

    // Set the translation import options. This determines if existing
    // translations will be overwritten by imported strings.
    $translationOptions = _locale_translation_default_update_options();

    locale_translation_clear_status();
    $batch = locale_translation_batch_update_build([], $langcodes, $translationOptions);
    batch_set($batch);

  }

}
