(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dsi_color_form = {
    attach: function attach(context, settings) {
      $('.form-date-picker').datepicker({
        todayHighlight: true,
        format: 'yyyy-mm-dd'
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
