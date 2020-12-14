
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.popover = {
    attach: function attach(context, settings) {
      $('.entity-popover').once('entity-popover').each(function() {
        $(this).parent().attr('data-toggle', 'popover');
      });
      $('[data-toggle="popover"]').once('popover-top').each(function () {
        let entity_type = $(this).parent().attr('entity-type');
        let entity_id = $(this).parent().attr('entity-id');
        let bundle = $(this).find('.entity-popover').data('bundle');
        let bundle_type = $(this).find('.entity-popover').data('bundle-type');
        $(this).popover({
          trigger: 'manual',
          placement: 'right',
          html: true,
          content: '<div id='+ entity_type + '-' + entity_id  + '-' + bundle_type + '-' + bundle + '></div>'
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
      // $('.popover-body').on("mouseleave", function () {
      //   var _this = this;
      //   setTimeout(function () {
      //     if (!$(".popover:hover").length) {
      //       $(_this).popover("hide")
      //     }
      //   }, 100);
      // });
      
      $('[data-toggle="popover"]').once('popover-id').mouseenter(function() {
        let entity_type = $(this).parent().attr('entity-type');
        let entity_id = $(this).parent().attr('entity-id');
        let bundle = $(this).find('.entity-popover').data('bundle');
        let bundle_type = $(this).find('.entity-popover').data('bundle-type');
        let entity_field = $(this).find('.entity-popover').data('entity-field');

        let url = Drupal.url('ajax/popover/'+ entity_type + '/' + entity_id + '/' + bundle_type + '/' + bundle);
        $.getJSON(url, function(data){
          let _content = '<div class="btn-group-vertical">';
          for (let i in data) {
            _content = _content + '<button class="btn entity-popover-button" data-entity-field="' + entity_field + '" data-entity-type="' + entity_type + '" data-entity-id="'+ entity_id +'" data-bundle-type="' + bundle_type +'" bundle="'+ bundle + '" data-id='+ i +'>' + data[i] + '</button>';
          }
          _content = _content + '</div>';
          let $xid = "#" + entity_type + '-' + entity_id  + '-' + bundle_type + '-' + bundle;
          $($xid).replaceWith(_content);
          Drupal.attachBehaviors($xid);
        });
      });
      $('.entity-popover-button').once('entity-popover-button').on('click', function () {
        let entity_type = $(this).data('entity-type'),
          entity_id = $(this).data('entity-id'),
          entity_field = $(this).data('entity-field'),
          bundle_type = $(this).data('bundle-type'),
          bundle = $(this).data('bundle'),
          id = $(this).data('id');
          text = $(this).text();
        var trList = $('.table-striped tr');
        console.log(trList);
        for (var i=0;i<trList.length;i++) {
          var tr_entity_id = trList.eq(i).attr('entity-id');
          if (entity_id == tr_entity_id){
            trList.eq(i).find('td').eq(3).find('span').text(text);
            console.log(tr_entity_id,text);
          }
        }
          $.ajax({
          type: "POST",
          url: Drupal.url('ajax/popover/' + entity_type + '/' + entity_id + '/' + bundle_type + '/' + bundle),
          data: "entity_field=" + entity_field + "&id=" + id,
          success: function success(response) {
            if(response[0] == 'success'){
              // alert('操作成功');
            }
          }
        });
      })
    }
  };
})(jQuery, Drupal, drupalSettings);
