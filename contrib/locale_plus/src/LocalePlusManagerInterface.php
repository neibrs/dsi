<?php

namespace Drupal\locale_plus;

interface LocalePlusManagerInterface {

  /**
   * locale update for languages.
   */
  function localeUpdate($options = ['langcodes' => 'zh-hans, en']);
}