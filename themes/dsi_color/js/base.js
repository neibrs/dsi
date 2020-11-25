(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.color_base = {
    attach: function attach(context, settings) {
      $(document).once('modal-z-index').ready(function () {
        $('#drupal-modal').parent().addClass('z-index-1030');
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
