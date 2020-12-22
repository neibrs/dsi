(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.color_base = {
    attach: function attach(context, settings) {
      $(document).once('modal-z-index').ready(function () {
        $('#drupal-modal').parent().addClass('z-index-1360');
      });

      $('.record-ajax-tr').once('record-ajax-tr').on('click', function() {
        let $tr = $(this).parent();
        let $entity_type = $tr.attr('entity-type');
        let $entity_id = $tr.attr('entity-id');
        let $td_count = $tr.children().length;

        let url = Drupal.url('ajax/dsi_record/'+ $entity_type + '/' + $entity_id + '/list');
        let $search_key = $(context).find('tr[entity-id-child='+ $entity_id +']');
        if ($search_key.length) {
          $search_key.remove();
        }
        else {
          $.get({
            url: url,
            success: function(data){
              let rs = $(data).find('#block-dsi-color-content').html();
              let content = '<tr entity-type='+ $entity_type + ' entity-id-child='+ $entity_id + '><td colspan='+ $td_count + '>' + rs + '</td></tr>';
              $tr.after(content);

              Drupal.attachBehaviors($tr, settings);
            }
          });
        }

      });
    }
  };
})(jQuery, Drupal, drupalSettings);
