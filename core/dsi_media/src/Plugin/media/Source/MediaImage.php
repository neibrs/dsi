<?php

namespace Drupal\dsi_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\Image as DrupalCoreMediaImage;

/**
 * Input-matching version of the Dsi Media Image media source.
 */
class MediaImage extends DrupalCoreMediaImage implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
