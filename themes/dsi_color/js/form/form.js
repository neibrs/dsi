(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dsi_color_form = {
    attach: function attach(context, settings) {
      $('.form-date-picker').datepicker({
        todayHighlight: true
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
