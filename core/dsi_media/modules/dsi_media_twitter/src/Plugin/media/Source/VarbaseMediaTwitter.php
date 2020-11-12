<?php

namespace Drupal\dsi_media_twitter\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\entity_browser_generic_embed\ValidationConstraintMatchTrait;
use Drupal\media_entity_twitter\Plugin\media\Source\Twitter as MediaEntityTwitter;

/**
 * Input-matching version of the Twitter media source.
 */
class DsiMediaTwitter extends MediaEntityTwitter implements InputMatchInterface {

  use ValidationConstraintMatchTrait;

}
