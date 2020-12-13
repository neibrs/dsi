
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.popover = {
    attach: function attach(context, settings) {
      $(context).find('.'+settings.popover_id).once('popover').each(function() {
        $(this).parent().attr('data-toggle', 'popover');
        $('[data-toggle="popover"]').once('popover-top').each(function () {
          let entity_type = $(this).parent().attr('entity-type');
          let entity_id = $(this).parent().attr('entity-id');
          $(this).popover({
            trigger: 'manual',
            placement: 'right',
            html: true,
            content: '<div id='+ entity_type + '-' + entity_id + '-' + settings.popover_id + '></div>'
          }).on("mouseenter", function () {
            var _this = this;
            $(this).popover("show");

            $(this).siblings(".popover").on("mouseleave", function () {
              $(_this).popover('hide');
            });
          }).on("mouseleave", function () {
            var _this = this;
            setTimeout(function () {
              if (!$(".popover:hover").length) {
                $(_this).popover("hide")
              }
            }, 100);
          });
        });

        $(this).once('popover-id').mouseenter(function() {
          let entity_type = $(this).parent().parent().attr('entity-type');
          let entity_id = $(this).parent().parent().attr('entity-id');
          let url = Drupal.url('ajax/popover/'+ entity_type + '/' + entity_id + '/' + settings.entity_type + '/' + settings.entity_bundle);
          $.getJSON(url, function(data){
            let _content = '<div class="btn-group-vertical">';
            for (let i in data) {
              _content = _content + '<button class="btn">' + data[i] + '</button>';
            }
            _content = _content + '</div>';
            $("#" + entity_type + '-' + entity_id + '-' + settings.popover_id).replaceWith(_content);
          });
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
