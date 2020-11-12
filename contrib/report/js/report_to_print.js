(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.reportToPrint = {
    attach: function attach(context) {
      var $context = $(context);
      
      $('.box.report-to-print').once('report-to-print').each(function () {
        if ($('ul.action-links a.report-to-print').length === 0) {
          $('ul.action-links').append('<li><a class="report-to-print btn btn-primary btn-sm">' + Drupal.t('Print') + '</a></li>');
        }
      });
  
      $('a.report-to-print').once().each(function () {
        $(this).click(function () {
          $('.box.report-to-print .box-body').jqprint({
            debug: false,
            importCSS: true,
            printContainer: true,
            operaSupport: false
          });
        });
      });
      
    }
  };
})(jQuery, Drupal, drupalSettings);