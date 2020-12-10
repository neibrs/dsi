
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.popover = {
    attach: function attach(context, settings) {
      $(context).find('.'+settings.popover_id).once('popover').each(function() {
        $(this).parent().attr('data-toggle', 'popover');

        // todo， add popover event.
        $('[data-toggle="popover"]').once('popover-top').each(function () {
          let entity_type = $(this).parent().attr('entity_type');
          let entity_id = $(this).parent().attr('entity_id');
          let url = Drupal.url('ajax/popover/'+ entity_type + '/' + entity_id + '/' + settings.entity_type + '/' + settings.entity_bundle);
          $(this).popover({
            trigger: 'manual',
            placement: 'right', //top, bottom, left or right
            // title: txt,
            html: true,
            content: function() {
              return '<div>xxx</div>';
              // $.ajax({
              //   type: 'GET',
              //   url: url,
              //   dataType: "html",
              //   success: function () {
              //     return '<div> xxx</div>';
              //   }
              // });
            }
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

      });
    }
  };
})(jQuery, Drupal, drupalSettings);
