(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.record_popover = {
    attach: function attach(context) {
      $(":checkbox").click(function(){
        console.log(123123);
      });
  
  
    }
  };
})(jQuery, Drupal, drupalSettings);