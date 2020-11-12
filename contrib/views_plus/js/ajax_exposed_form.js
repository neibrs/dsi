(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.ajaxExposedForm = {
    attach: function attach(context) {
      $(context).find('.ajax-submit').once('ajax-submit').each(Drupal.ajaxSubmit);
    }
  };

  Drupal.ajaxSubmit = function() {
    var self = this;

    $(this).on('click', function (e) {
      var $form = $(self).parents('form');
      var action = $form.attr('action');
      var method = 'GET';//$form.attr('method');
      var data = $form.serialize();
      var rel = $(self).attr('rel');
      $(rel).html('<div class="ajax-links-api-loading"></div>');
      $.ajax({
        url : action,
        type: method,
        data: data,
        success: function success(response) {
          var new_content = $(response).find(rel);
          $(rel).html(new_content.html());
          Drupal.attachBehaviors(rel);
        }
      });
      e.preventDefault();
    });
  }
})(jQuery, Drupal, drupalSettings);
