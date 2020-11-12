<?php

namespace Drupal\dsi_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\VideoFile as DrupalCoreMediaVideoFile;

/**
 * Input-matching version of the Dsi Media Video File media source.
 */
class MediaVideoFile extends DrupalCoreMediaVideoFile implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
