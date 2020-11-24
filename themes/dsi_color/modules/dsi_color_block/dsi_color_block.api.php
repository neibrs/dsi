<?php

function hook_navbar_user_block_item() {
  return [
    'inline' => [
      'label' => t('Inline'),
      'library' => [
        'name/widget.inline',
      ],
      'wrapper_attributes' => [
        'class' => ['form--inline', 'clearfix'],
      ],
    ],
  ];
}
