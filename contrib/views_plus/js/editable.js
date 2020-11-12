(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.editable = {
    attach: function attach(context) {

      $(context).find('.data-update-entity').once('update-entity').each(function () {
        var $text_field = $(this);
        $text_field.attr('data-old-value', $text_field.val());
        $text_field.focusout(function (event) {
          var $this = $(this);
          if ($this.attr('data-old-value') !== $this.val()) {
            $.ajax({
              type: 'POST',
              url: Drupal.url('views_plus/js/update_editable_field'),
              data: {
                entity_type: $text_field.attr('data-update-entity'),
                entity_field: $text_field.attr('data-update-field'),
                keys: JSON.parse($text_field.attr('data-update-keys')),
                value: $this.val(),
              },
              dataType: 'json',
            });
            $(this).attr('data-old-value', $text_field.val());
          }
        });
        $text_field.keypress(function (event) {
          if (event.which === 13) {
            // TODO: 移动焦点

            return false;
          }
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
