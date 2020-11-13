(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.custom_script = {
    attach: function attach(context) {
      // Apply for oams.
      $(context).find('.brand-sidebar').once('brand-sidebar').addClass('.new-left');

      // Click menu link.
      $(context).find('.left-item').once('left-item').click(function () {
        let link_url = $(this).find('.item-name').data('url');
        window.location.href = link_url;
      });


      $('.views-table.views-view-table').DataTable({
        "responsive": true,
      });

      $(context).find('.views-table.views-view-table tr').once('views-view-table-tr').click(function () {
        let $id = $(this).attr('entity-id');
        window.location.href = Drupal.url('dsi_client/' + $id);
      });

      // For local tasks block
      $('.tabs .tab a').once('nav-min-li-a').click(function() {
        console.log($(this).attr('href'));
        window.location.href = $(this).attr('href');
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
