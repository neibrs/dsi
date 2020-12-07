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
  $(":checkbox").off("click").on("click",function() {
    console.log(context);
    var element = $(this);
    var id = element.attr('data-id');
    if (id != null && id != ''){
       var state = 0;
       if (element.is(":checked")){
         var state = 1;
       }
      $.ajax({
        //提交数据的类型 POST GET
        type:"POST",
        //提交的网址
        url:"ajax/dsi_record/"+id+"/"+state+"/setStatus",
        //提交的数据   该参数为属性值类型的参数         //（和url?Name="sanmao"&Password="sanmapword"一样）后台若为SpringMVC接受，注明@RequestParam
        data:{},
        //返回数据的格式
        datatype: "json",//"xml", "html", "script", "json", "jsonp", "text".
        //成功返回之后调用的函数
        success:function(data){
          alert(data.massage);
          console.log(data);
        }
      });
    }
    console.log(id,state);
        });
      }
    };
})(jQuery, Drupal, drupalSettings);
