<?php

function template_preprocess_box(&$variables) {
  $variables['attributes']['class'][] = 'box';
}
