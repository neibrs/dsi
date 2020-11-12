<?php

function hook_workbench_view(&$elements) {
  $elements['foo'] = [
    '#markup' => 'bar',
  ];
}
