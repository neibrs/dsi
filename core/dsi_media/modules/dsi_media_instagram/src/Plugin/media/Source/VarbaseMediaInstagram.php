<?php

namespace Drupal\dsi_media_instagram\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\entity_browser_generic_embed\ValidationConstraintMatchTrait;
use Drupal\media_entity_instagram\Plugin\media\Source\Instagram as MediaEntityInstagram;

/**
 * Input-matching version of the Instagram media source.
 */
class DsiMediaInstagram extends MediaEntityInstagram implements InputMatchInterface {

  use ValidationConstraintMatchTrait;

}
