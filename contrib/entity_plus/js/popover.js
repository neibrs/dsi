
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.popover = {
    attach: function attach(context, settings) {
      $(context).find('.'+settings.popover_id).once('popover').each(function() {
        $(this).parent().attr('data-toggle', 'popover');
        $(this).parent().attr('data-html', true);
        $(this).parent().attr('data-container', 'body');
        $(this).attr('data-html', true);
        
        // todo， add popover event.
        $('[data-toggle="popover"]').once('popover-top').each(function () {
          let entity_type = $(this).parent().attr('entity-type');
          let entity_id = $(this).parent().attr('entity-id');
          // console.log(entity_type,entity_id);
          let url = Drupal.url('ajax/popover/'+ entity_type + '/' + entity_id + '/' + settings.entity_type + '/' + settings.entity_bundle);
          $(this).popover({
            trigger: 'manual',
            placement: 'right', //top, bottom, left or right
            // title: txt,
            html: true,
            content: function() {
              // return '<div>aaaaaaa</div>';
              // return '<h1>ccc</h1>';
              // var entityElementSelector = "[data-quickedit-entity-id=\"".concat(entityID, "\"]");
              // return '<div>ccccccccccccccc</div>';
  
              // return '<form><input type="text" value="值啊"><input type="submit" value="T" ></form>';
              return '<h1>aaa</h1><form><input type="text" value="值啊"><input type="submit" value="T" ></form><h1>bbb</h1>';
              $.get(url, { name: "John", time: "2pm" } ,function(data){
                let form = $(data).find('form.entity-popover-form').parent().html();
                console.log(form);
              });
              console.log('end');
              return;
              return form;
  
              $.ajax({
                type: 'GET',
                url: url,
                dataType: "html",
                success: function (data) {
                  return '<div>Ajax</div>';
                  let form = $(data).find('form.entity-popover-form').parent().html();
                  console.log('<form><input type="checkbox"><input type="text"></form>');
                  // console.log($(data).find("#block-dsi-color-content").html());
                  // let xx = '<div>xxxxx</div>';
                  return data;
                },
              });
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
