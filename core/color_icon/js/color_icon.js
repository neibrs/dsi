(function ($, Drupal) {
  Drupal.behaviors.colorSquare = {
    attach: function attach(context, settings) {
      $(context).find('.color-square, .color-rectangle').once('color-square').each(function () {
        var color = $(this).attr('data-fill-color');
        $(this).css('background-color', color);
      });
    }
  };

})(jQuery, Drupal);