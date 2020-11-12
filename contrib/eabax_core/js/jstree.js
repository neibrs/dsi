(function ($, Drupal) {
  Drupal.behaviors.jstree = {
    attach: function attach (context) {
      $(context).find('.jstree').once('jstree').each(function() {
        $(this).jstree();
        $(this).on('changed.jstree', function (e, data) {
          var href = data.node.a_attr.href;
          window.open(href, '_self');
        });
      });
    }
  }
})(jQuery, Drupal);
