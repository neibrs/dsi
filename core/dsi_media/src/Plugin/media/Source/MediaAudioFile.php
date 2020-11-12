<?php

namespace Drupal\dsi_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\AudioFile as DrupalCoreMediaAudioFile;

/**
 * Input-matching version of the Dsi Media Audio File media source.
 */
class MediaAudioFile extends DrupalCoreMediaAudioFile implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
