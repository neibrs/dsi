(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.record_popover = {
    attach: function attach(context) {
      $('[data-toggle="popover"]').each(function () {
        var element = $(this);
            var id = element.attr('id');
            var txt = element.html();
            element.popover({
                trigger: 'manual',
                placement: 'right', //top, bottom, left or right
             // title: txt,
                html: true,
                content: ContentMethod(txt),

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

  function ContentMethod(txt) {
    var html =
      '<div class="fc-event-icon" id="record-popover">' +
      '<i class="fas fa-circle fa-fw f-s-9 text-grey" ></i> &emsp;' +
      '<a style="text-decoration:none; color:#000000 ">停用</a>' +
      '</div>' +
      '<div class="fc-event-icon" id="record-popover">' +
      '<i class="fas fa-circle fa-fw f-s-9 text-green" ></i> &emsp;'  +
      '<a style="text-decoration:none; color:#000000">启用</a>'+
      '</div>' ;

    return html;
            }
        }
    };
})(jQuery, Drupal, drupalSettings);
