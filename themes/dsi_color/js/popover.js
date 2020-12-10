(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.color_popover = {
    attach: function attach(context) {
      $(context).find('.importance-popover, .cooperating-popover').once('color_popover').hover(function() {
        $(this).attr("data-toggle", "popover");

        $('[data-toggle="popover"]').once('data-popover').each(function () {
          $(this).popover({
            trigger: 'manual',
            placement: 'right',
            // title: txt,
            html: true,
            content: ContentMethod(txt),
          });
        });
      });
      function ContentMethod(txt) {
        return 'xxx';
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
