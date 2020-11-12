/**
 * @file
 * Attaches behaviors for views select.
 */
(function ($, Drupal) {
  Drupal.behaviors.viewsSelect = {
    attach: function attach(context, settings) {
      $(context).find('table tr').once('viewsSelect').on('click', function () {
        var $this = $(this);
        var entity_id = $this.attr('entity-id');
        var hidden_input = $(this).parent().parent().parent().parent().parent().parent().find('input[type="hidden"]');
        hidden_input.attr('value', entity_id);
        $this.addClass('highlighted-input').siblings().removeClass("highlighted-input");
      });
    },
  };
})(jQuery, Drupal);
