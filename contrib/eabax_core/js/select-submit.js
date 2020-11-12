(function ($, Drupal) {
  Drupal.behaviors.selectSubmit = {
    attach: function attach(context, settings) {
      $(context).find('.select-submit').once('select-submit').on('change', function() {
        var $form = $(this).parents('form');
        $form.submit();
      });
    }
  };
})(jQuery, Drupal);
