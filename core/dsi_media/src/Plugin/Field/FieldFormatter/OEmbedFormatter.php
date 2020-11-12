<?php

namespace Drupal\dsi_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Url;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\media\Plugin\Field\FieldFormatter\OEmbedFormatter as OEmbedFormatterBase;

/**
 * Plugin implementation of the 'oembed' formatter for Dsi.
 *
 * @internal
 *   This is an internal part of the oEmbed system and should only be used by
 *   oEmbed-related code in Dsi.
 *
 * @FieldFormatter(
 *   id = "dsi_oembed",
 *   label = @Translation("Dsi oEmbed content"),
 *   field_types = {
 *     "link",
 *     "string",
 *     "string_long",
 *   },
 * )
 */
class OEmbedFormatter extends OEmbedFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $max_width = $this->getSetting('max_width');
    $max_height = $this->getSetting('max_height');

    foreach ($items as $delta => $item) {
      $main_property = $item->getFieldDefinition()->getFieldStorageDefinition()->getMainPropertyName();
      $value = $item->{$main_property};

      if (empty($value)) {
        continue;
      }

      $media = $item->getEntity();
      $provider = $media->field_provider->value;

      $url = Url::fromRoute('media.oembed_iframe', [], [
        'query' => [
          'url' => $value,
          'max_width' => $max_width,
          'max_height' => $max_height,
          'type' => "remote_video",
          'provider' => strtolower($provider),
          'hash' => $this->iFrameUrlHelper->getHash($value, $max_width, $max_height, $provider),
        ],
      ]);

      // Render videos and rich content in an iframe for security reasons.
      // @see: https://oembed.com/#section3
      $element[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'src' => $url->toString(),
          'frameborder' => 0,
          'scrolling' => FALSE,
          'allowtransparency' => TRUE,
          'width' => $max_width,
          'height' => $max_height,
          'class' => ['media-oembed-content'],
        ],
        '#attached' => [
          'library' => [
            'media/oembed.formatter',
          ],
        ],
      ];

    }
    return $element;
  }

}
