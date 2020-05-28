(function ($, Drupal) {
  Drupal.behaviors.tree = {
    attach: function attach(context) {
      $(context).find('.menu-item-folder > span').on('click', function () {
        var $menu_item = $(this).parent();
        if ($menu_item.hasClass('menu-item--collapsed')) {
          $menu_item.removeClass('menu-item--collapsed');
          $menu_item.addClass('menu-item--expanded');
        }
        else if ($menu_item.hasClass('menu-item--expanded')) {
          $menu_item.removeClass('menu-item--expanded');
          $menu_item.addClass('menu-item--collapsed');
        }
      });
    }
  };
})(jQuery, Drupal);